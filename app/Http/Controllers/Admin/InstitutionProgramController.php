<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\InstitutionMember;
use App\Models\InstitutionProgram;
use App\Models\InstitutionProgramParticipant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InstitutionProgramController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isAdmin() && ! $user->hasPermission('manage.packages') && ! $user->hasPermission('manage.institutions'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $q = InstitutionProgram::query()
            ->with(['institution:id,name_ar,org_type', 'instructor:id,name'])
            ->latest();

        if ($request->filled('status') && array_key_exists($request->status, InstitutionProgram::statuses())) {
            $q->where('status', $request->status);
        }
        if ($request->filled('service_key') && in_array($request->service_key, InstitutionProgram::serviceKeys(), true)) {
            $q->where('service_key', $request->service_key);
        }
        if ($request->filled('kind') && in_array($request->kind, [InstitutionProgram::KIND_TRAINING, InstitutionProgram::KIND_DEVELOPMENT], true)) {
            $q->where('program_kind', $request->kind);
        }

        $programs = $q->paginate(25)->withQueryString();

        $stats = [];
        foreach (array_keys(InstitutionProgram::statuses()) as $st) {
            if ($st === InstitutionProgram::STATUS_CANCELLED) {
                continue;
            }
            $stats[$st] = InstitutionProgram::where('status', $st)->count();
        }

        return view('admin.institution-programs.index', [
            'programs' => $programs,
            'statuses' => InstitutionProgram::statuses(),
            'serviceLabels' => InstitutionProgram::serviceLabels(),
            'stats' => $stats,
            'filters' => $request->only(['status', 'service_key', 'kind']),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.institution-programs.create', $this->formData([
            'institution_id' => $request->integer('institution_id') ?: null,
            'program_kind' => in_array($request->input('program_kind'), [
                InstitutionProgram::KIND_TRAINING,
                InstitutionProgram::KIND_DEVELOPMENT,
            ], true) ? $request->input('program_kind') : 'training',
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $data['currency'] = strtoupper(trim((string) ($data['currency'] ?? 'QAR'))) ?: 'QAR';
        $data['program_kind'] = $this->resolveKind($data['service_key'], $data['program_kind'] ?? null);
        $data['progress_percent'] = 0;

        $program = InstitutionProgram::create($data);

        return redirect()
            ->route('admin.institution-programs.show', $program)
            ->with('success', 'تم إنشاء البرنامج / المشروع.');
    }

    public function show(InstitutionProgram $institutionProgram): View
    {
        $institutionProgram->load([
            'institution.members',
            'participants',
            'coordinator:id,name,email',
            'instructor:id,name,email',
        ]);

        return view('admin.institution-programs.show', [
            'program' => $institutionProgram,
            'statuses' => InstitutionProgram::statuses(),
            'serviceLabels' => InstitutionProgram::serviceLabels(),
            'deliveryModes' => InstitutionProgram::deliveryModes(),
            'participantStatuses' => InstitutionProgramParticipant::STATUSES,
            'members' => $institutionProgram->institution
                ? $institutionProgram->institution->members()->where('is_active', true)->orderBy('name')->get()
                : collect(),
        ]);
    }

    public function edit(InstitutionProgram $institutionProgram): View
    {
        return view('admin.institution-programs.edit', $this->formData([], $institutionProgram));
    }

    public function update(Request $request, InstitutionProgram $institutionProgram): RedirectResponse
    {
        $data = $this->validated($request);
        $data['currency'] = strtoupper(trim((string) ($data['currency'] ?? 'QAR'))) ?: 'QAR';
        $data['program_kind'] = $this->resolveKind($data['service_key'], $data['program_kind'] ?? null);

        $previousStatus = (string) $institutionProgram->status;

        if (isset($data['status']) && (string) $data['status'] !== $previousStatus
            && ! $institutionProgram->canTransitionTo((string) $data['status'])) {
            return back()->with('error', 'انتقال غير مسموح من «'.$institutionProgram->statusLabel().'» إلى الحالة المطلوبة.')->withInput();
        }

        if (($data['status'] ?? null) === InstitutionProgram::STATUS_COMPLETED && ! $institutionProgram->completed_at) {
            $data['completed_at'] = now();
            $data['progress_percent'] = 100;
        }

        $institutionProgram->update($data);

        if (isset($data['status']) && (string) $data['status'] !== $previousStatus) {
            try {
                event(new \App\Events\InstitutionProgramStatusChanged(
                    $institutionProgram->fresh(['institution', 'createdBy']),
                    $previousStatus,
                    (string) $data['status']
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route('admin.institution-programs.show', $institutionProgram)
            ->with('success', 'تم تحديث البرنامج.');
    }

    public function updateStatus(Request $request, InstitutionProgram $institutionProgram): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(InstitutionProgram::statuses()))],
        ]);

        $previousStatus = (string) $institutionProgram->status;

        if (! $institutionProgram->canTransitionTo($data['status'])) {
            return back()->with('error', 'انتقال غير مسموح من «'.$institutionProgram->statusLabel().'» إلى الحالة المطلوبة.');
        }

        $payload = ['status' => $data['status']];
        if ($data['status'] === InstitutionProgram::STATUS_COMPLETED) {
            $payload['completed_at'] = now();
            $payload['progress_percent'] = 100;
        }
        if ($data['status'] === InstitutionProgram::STATUS_IN_PROGRESS && $institutionProgram->progress_percent < 5) {
            $payload['progress_percent'] = 10;
        }

        $institutionProgram->update($payload);

        if ((string) $data['status'] !== $previousStatus) {
            try {
                event(new \App\Events\InstitutionProgramStatusChanged(
                    $institutionProgram->fresh(['institution', 'createdBy']),
                    $previousStatus,
                    (string) $data['status']
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'تم تحديث الحالة إلى: '.$institutionProgram->fresh()->statusLabel());
    }

    public function storeParticipant(Request $request, InstitutionProgram $institutionProgram): RedirectResponse
    {
        $data = $request->validate([
            'institution_member_id' => ['nullable', 'exists:institution_members,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        if (! empty($data['institution_member_id'])) {
            $member = InstitutionMember::findOrFail($data['institution_member_id']);
            abort_if(
                $institutionProgram->institution_id
                && (int) $member->institution_id !== (int) $institutionProgram->institution_id,
                422
            );
            $data['name'] = $data['name'] ?: $member->displayName();
            $data['email'] = $data['email'] ?: $member->email;
            $data['phone'] = $data['phone'] ?: $member->phone;
            $data['user_id'] = $data['user_id'] ?: $member->user_id;
        } elseif (! empty($data['user_id'])) {
            $user = User::findOrFail($data['user_id']);
            $data['name'] = $data['name'] ?: $user->name;
            $data['email'] = $data['email'] ?: $user->email;
            $data['phone'] = $data['phone'] ?: $user->phone;
        }

        if (blank($data['name'] ?? null)) {
            return back()->withErrors(['name' => 'اسم المشارك مطلوب.'])->withInput();
        }

        $institutionProgram->participants()->create([
            'institution_member_id' => $data['institution_member_id'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'status' => 'enrolled',
            'progress_percent' => 0,
        ]);

        return back()->with('success', 'تمت إضافة المشارك إلى البرنامج.');
    }

    public function updateParticipant(Request $request, InstitutionProgramParticipant $participant): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(InstitutionProgramParticipant::STATUSES))],
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $participant->update([
            'status' => $data['status'],
            'progress_percent' => $data['progress_percent'] ?? $participant->progress_percent,
            'notes' => $data['notes'] ?? $participant->notes,
        ]);

        $participant->program?->recalculateProgress();

        return back()->with('success', 'تم تحديث تقدّم المشارك.');
    }

    public function destroyParticipant(InstitutionProgramParticipant $participant): RedirectResponse
    {
        $program = $participant->program;
        $participant->delete();
        $program?->recalculateProgress();

        return back()->with('success', 'تم حذف المشارك.');
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @return array<string, mixed>
     */
    private function formData(array $defaults = [], ?InstitutionProgram $program = null): array
    {
        return [
            'program' => $program,
            'defaults' => $defaults,
            'institutions' => Institution::query()->where('is_active', true)->orderBy('name_ar')->get(['id', 'name_ar', 'org_type']),
            'serviceLabels' => InstitutionProgram::serviceLabels(),
            'statuses' => InstitutionProgram::statuses(),
            'deliveryModes' => InstitutionProgram::deliveryModes(),
            'instructors' => User::query()
                ->whereIn('role', ['instructor', 'teacher'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
            'users' => User::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->limit(300)
                ->get(['id', 'name', 'email']),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'service_key' => ['required', Rule::in(InstitutionProgram::serviceKeys())],
            'program_kind' => ['nullable', Rule::in([InstitutionProgram::KIND_TRAINING, InstitutionProgram::KIND_DEVELOPMENT])],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'summary_ar' => ['nullable', 'string'],
            'summary_en' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_keys(InstitutionProgram::statuses()))],
            'planned_participants' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'duration_hours' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'delivery_mode' => ['nullable', Rule::in(array_keys(InstitutionProgram::deliveryModes()))],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'scheduled_at' => ['nullable', 'date'],
            'inquiry_notes' => ['nullable', 'string'],
            'proposal_notes' => ['nullable', 'string'],
            'diagnosis_notes' => ['nullable', 'string'],
            'improvement_plan' => ['nullable', 'string'],
            'result_notes' => ['nullable', 'string'],
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'coordinator_user_id' => ['nullable', 'exists:users,id'],
            'assigned_instructor_id' => ['nullable', 'exists:users,id'],
        ], [
            'title_ar.required' => 'عنوان البرنامج مطلوب.',
            'service_key.required' => 'اختر نوع الخدمة.',
        ]);
    }

    private function resolveKind(string $serviceKey, ?string $kind): string
    {
        if (in_array($serviceKey, ['institutional_development', 'needs_assessment', 'followup_evaluation'], true)) {
            return InstitutionProgram::KIND_DEVELOPMENT;
        }

        return $kind === InstitutionProgram::KIND_DEVELOPMENT
            ? InstitutionProgram::KIND_DEVELOPMENT
            : InstitutionProgram::KIND_TRAINING;
    }
}
