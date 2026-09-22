<?php

namespace App\Http\Controllers\Instructor;

use App\Events\InstitutionProgramStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\InstitutionProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * تعاقد مباشر: برامج الجهات المسندة للمدرب للتنفيذ والمتابعة.
 */
class InstitutionDeliveryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $programs = InstitutionProgram::query()
            ->where('assigned_instructor_id', $user->id)
            ->with(['institution:id,name_ar,name_en,default_engagement_mode'])
            ->latest()
            ->paginate(20);

        return view('instructor.institution-delivery.index', [
            'programs' => $programs,
        ]);
    }

    public function show(Request $request, InstitutionProgram $program): View
    {
        $this->authorizeAssignment($request, $program);

        $program->load(['institution', 'participants' => fn ($q) => $q->orderBy('name')]);

        return view('instructor.institution-delivery.show', [
            'program' => $program,
            'isDirect' => $program->isDirectDelivery(),
            'modeLabel' => $program->engagementModeLabel(),
        ]);
    }

    public function update(Request $request, InstitutionProgram $program): RedirectResponse
    {
        $this->authorizeAssignment($request, $program);

        $data = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(InstitutionProgram::statuses()))],
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'result_notes' => ['nullable', 'string', 'max:10000'],
            'improvement_plan' => ['nullable', 'string', 'max:10000'],
        ]);

        $previous = (string) $program->status;
        $payload = array_filter([
            'result_notes' => $data['result_notes'] ?? null,
            'improvement_plan' => $data['improvement_plan'] ?? null,
            'progress_percent' => array_key_exists('progress_percent', $data) ? (int) $data['progress_percent'] : null,
        ], fn ($v) => $v !== null);

        if (! empty($data['status']) && $data['status'] !== $program->status) {
            if (! $program->canTransitionTo($data['status'])) {
                return back()->with('error', 'انتقال الحالة غير مسموح من «'.$program->statusLabel().'».');
            }
            $payload['status'] = $data['status'];
            if ($data['status'] === InstitutionProgram::STATUS_COMPLETED) {
                $payload['completed_at'] = now();
                $payload['progress_percent'] = $payload['progress_percent'] ?? 100;
            }
            if ($data['status'] === InstitutionProgram::STATUS_IN_PROGRESS && $program->status !== InstitutionProgram::STATUS_IN_PROGRESS) {
                // start delivery
            }
        }

        $program->update($payload);

        if (isset($payload['status']) && $payload['status'] !== $previous) {
            try {
                event(new InstitutionProgramStatusChanged(
                    $program->fresh(['institution', 'createdBy']),
                    $previous,
                    $payload['status']
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'تم تحديث تنفيذ التعاقد.');
    }

    private function authorizeAssignment(Request $request, InstitutionProgram $program): void
    {
        abort_unless(
            (int) $program->assigned_instructor_id === (int) $request->user()->id,
            403,
            'هذا البرنامج غير مسند إليك.'
        );
    }
}
