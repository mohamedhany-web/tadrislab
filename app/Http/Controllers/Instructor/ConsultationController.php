<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\ConsultationRequest;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:instructor|teacher']);
    }

    public function index()
    {
        $user = Auth::user();

        abort_unless(
            ! $user->hasGrantedServices() || $user->canDeliverService('consultations'),
            403,
            'لم تُسند لك خدمة الاستشارات بعد.'
        );

        $requests = ConsultationRequest::query()
            ->where('instructor_id', $user->id)
            ->with(['student', 'classroomMeeting', 'service'])
            ->latest()
            ->paginate(20);

        $upcoming = ConsultationRequest::query()
            ->where('instructor_id', $user->id)
            ->whereNotNull('preferred_slot_at')
            ->where('preferred_slot_at', '>=', now())
            ->whereIn('status', [
                ConsultationRequest::STATUS_NEW,
                ConsultationRequest::STATUS_CONFIRMED,
                ConsultationRequest::STATUS_RESCHEDULED,
                ConsultationRequest::STATUS_SCHEDULED,
            ])
            ->orderBy('preferred_slot_at')
            ->limit(8)
            ->get();

        return view('instructor.consultations.index', compact('requests', 'upcoming'));
    }

    public function show(ConsultationRequest $consultation)
    {
        if ((int) $consultation->instructor_id !== (int) Auth::id()) {
            abort(403);
        }

        $consultation->load(['student', 'classroomMeeting', 'service']);

        return view('instructor.consultations.show', compact('consultation'));
    }
}
