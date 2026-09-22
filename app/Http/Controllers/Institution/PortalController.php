<?php

namespace App\Http\Controllers\Institution;

use App\Events\InstitutionProgramStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\InstitutionMember;
use App\Models\InstitutionProgram;
use App\Models\InstitutionProgramParticipant;
use App\Models\User;
use App\Services\InstitutionEngagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * لوحة منسق الجهة — تعاقد منصة (مقاعد/تقدّم/تقارير) + متابعة تعاقد مباشر.
 */
class PortalController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $memberships = InstitutionMember::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->with('institution')
            ->get();

        if ($memberships->isEmpty()) {
            return view('institution.portal.empty');
        }

        if ($memberships->count() === 1) {
            return redirect()->route('institution.portal.show', $memberships->first()->institution);
        }

        return view('institution.portal.index', compact('memberships'));
    }

    public function show(Request $request, Institution $institution): View
    {
        $member = $this->authorizeMembership($request, $institution);

        $institution->load([
            'members' => fn ($q) => $q->where('is_active', true)->orderBy('member_role')->orderBy('name'),
            'programs' => fn ($q) => $q->with('instructor:id,name')->latest()->limit(40),
        ]);

        $report = InstitutionEngagementService::coordinatorReport($institution);

        return view('institution.portal.show', [
            'institution' => $institution,
            'member' => $member,
            'isCoordinator' => $member->member_role === InstitutionMember::ROLE_COORDINATOR,
            'report' => $report,
            'engagementModes' => InstitutionEngagementService::modes(),
        ]);
    }

    public function report(Request $request, Institution $institution): View
    {
        $member = $this->authorizeMembership($request, $institution, requireCoordinator: true);
        $report = InstitutionEngagementService::coordinatorReport($institution);

        return view('institution.portal.report', [
            'institution' => $institution,
            'member' => $member,
            'report' => $report,
        ]);
    }

    public function storeParticipant(Request $request, Institution $institution): RedirectResponse
    {
        $this->authorizeMembership($request, $institution, requireCoordinator: true);

        if ($institution->isPlatformAccessDefault()
            && ! InstitutionEngagementService::canAddOrgParticipant($institution)) {
            return back()->with('error', 'تم بلوغ حد مقاعد المنصة لهذه الجهة. زد الحد من الإدارة أو أوقف مشاركين.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'title' => ['nullable', 'string', 'max:120'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        InstitutionMember::query()->updateOrCreate(
            [
                'institution_id' => $institution->id,
                'email' => $data['email'],
            ],
            [
                'user_id' => $user?->id,
                'member_role' => InstitutionMember::ROLE_PARTICIPANT,
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'title' => $data['title'] ?? null,
                'is_active' => true,
            ]
        );

        return back()->with('success', 'تم إضافة المشارك. إن كان لديه حساب على المنصة سيظهر له الوصول عبر عضويته.');
    }

    public function showProgram(Request $request, Institution $institution, InstitutionProgram $program): View
    {
        $member = $this->authorizeMembership($request, $institution);
        abort_unless((int) $program->institution_id === (int) $institution->id, 404);

        $program->load(['participants' => fn ($q) => $q->orderBy('name'), 'instructor:id,name']);
        $orgMembers = $institution->members()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('institution.portal.program', [
            'institution' => $institution,
            'program' => $program,
            'member' => $member,
            'isCoordinator' => $member->member_role === InstitutionMember::ROLE_COORDINATOR,
            'orgMembers' => $orgMembers,
            'isPlatform' => $program->isPlatformAccess(),
            'isDirect' => $program->isDirectDelivery(),
            'seatsRemaining' => $program->seatsRemaining(),
            'seatCap' => InstitutionEngagementService::seatCap($program),
        ]);
    }

    public function acceptProposal(Request $request, Institution $institution, InstitutionProgram $program): RedirectResponse
    {
        $this->authorizeMembership($request, $institution, requireCoordinator: true);
        abort_unless((int) $program->institution_id === (int) $institution->id, 404);

        if (! $program->canCoordinatorAccept()) {
            return back()->with('error', 'لا يمكن قبول هذا البرنامج في حالته الحالية.');
        }

        $previous = (string) $program->status;
        $program->update(['status' => InstitutionProgram::STATUS_APPROVED]);

        $this->fireStatusChanged($program, $previous, InstitutionProgram::STATUS_APPROVED);

        $hint = $program->isDirectDelivery()
            ? 'تم قبول العرض. سيُنفَّذ عبر المدرب المعيَّن.'
            : 'تم قبول العرض. يمكنك تفعيل المشاركين ضمن المقاعد المتاحة.';

        return back()->with('success', $hint);
    }

    public function rejectProposal(Request $request, Institution $institution, InstitutionProgram $program): RedirectResponse
    {
        $this->authorizeMembership($request, $institution, requireCoordinator: true);
        abort_unless((int) $program->institution_id === (int) $institution->id, 404);

        if (! $program->canCoordinatorReject()) {
            return back()->with('error', 'لا يمكن رفض هذا البرنامج في حالته الحالية.');
        }

        $previous = (string) $program->status;
        $program->update(['status' => InstitutionProgram::STATUS_CANCELLED]);

        $this->fireStatusChanged($program, $previous, InstitutionProgram::STATUS_CANCELLED);

        return back()->with('success', 'تم رفض/إلغاء الطلب.');
    }

    public function enrollParticipant(Request $request, Institution $institution, InstitutionProgram $program): RedirectResponse
    {
        $this->authorizeMembership($request, $institution, requireCoordinator: true);
        abort_unless((int) $program->institution_id === (int) $institution->id, 404);
        abort_unless($program->isDeliverable() || $program->status === InstitutionProgram::STATUS_APPROVED, 403);

        if ($program->isDirectDelivery()) {
            return back()->with('error', 'هذا البرنامج تعاقد مباشر — التنفيذ عبر المدرب، وليس بتفعيل مقاعد مشاركين.');
        }

        if (! InstitutionEngagementService::canEnrollMore($program)) {
            return back()->with('error', 'لا مقاعد متبقية في هذا البرنامج. راجع حد المقاعد مع الإدارة.');
        }

        $data = $request->validate([
            'institution_member_id' => [
                'required',
                'integer',
                Rule::exists('institution_members', 'id')->where('institution_id', $institution->id),
            ],
        ]);

        $orgMember = InstitutionMember::query()->findOrFail($data['institution_member_id']);

        InstitutionProgramParticipant::query()->updateOrCreate(
            [
                'institution_program_id' => $program->id,
                'institution_member_id' => $orgMember->id,
            ],
            [
                'user_id' => $orgMember->user_id,
                'name' => $orgMember->displayName(),
                'email' => $orgMember->email,
                'phone' => $orgMember->phone,
                'status' => 'enrolled',
                'progress_percent' => 0,
            ]
        );

        $program->recalculateProgress();

        return back()->with('success', 'تم تفعيل المشارك على مقعد البرنامج.');
    }

    public function updateParticipantProgress(
        Request $request,
        Institution $institution,
        InstitutionProgram $program,
        InstitutionProgramParticipant $participant
    ): RedirectResponse {
        $this->authorizeMembership($request, $institution, requireCoordinator: true);
        abort_unless((int) $program->institution_id === (int) $institution->id, 404);
        abort_unless((int) $participant->institution_program_id === (int) $program->id, 404);

        if ($program->isDirectDelivery()) {
            return back()->with('error', 'تقدّم التعاقد المباشر يحدّثه المدرب المنفّذ.');
        }

        $data = $request->validate([
            'progress_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(InstitutionProgramParticipant::STATUSES))],
        ]);

        $payload = ['progress_percent' => (int) $data['progress_percent']];
        if (! empty($data['status'])) {
            $payload['status'] = $data['status'];
        } elseif ((int) $data['progress_percent'] >= 100) {
            $payload['status'] = 'completed';
        } elseif ((int) $data['progress_percent'] > 0) {
            $payload['status'] = 'attended';
        }

        $participant->update($payload);
        $program->recalculateProgress();

        if ($program->fresh()->progress_percent >= 100
            && $program->status === InstitutionProgram::STATUS_IN_PROGRESS) {
            $previous = (string) $program->status;
            $program->update([
                'status' => InstitutionProgram::STATUS_COMPLETED,
                'completed_at' => now(),
                'progress_percent' => 100,
            ]);
            $this->fireStatusChanged($program, $previous, InstitutionProgram::STATUS_COMPLETED);
        }

        return back()->with('success', 'تم تحديث تقدّم المشارك.');
    }

    private function fireStatusChanged(InstitutionProgram $program, string $from, string $to): void
    {
        try {
            event(new InstitutionProgramStatusChanged(
                $program->fresh(['institution', 'createdBy']),
                $from,
                $to
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function authorizeMembership(
        Request $request,
        Institution $institution,
        bool $requireCoordinator = false
    ): InstitutionMember {
        $member = InstitutionMember::query()
            ->where('institution_id', $institution->id)
            ->where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->first();

        abort_unless($member, 403);
        if ($requireCoordinator) {
            abort_unless($member->member_role === InstitutionMember::ROLE_COORDINATOR, 403);
        }

        return $member;
    }
}
