<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeeting;
use App\Models\ConsultationRequest;
use App\Models\ConsultationService;
use App\Models\ConsultationSetting;
use App\Models\AgreementPayment;
use App\Models\InstructorAgreement;
use App\Models\Notification;
use App\Models\WalletTransaction;
use App\Services\ConsultationNotificationService;
use App\Services\OneToOneAvailabilityService;
use App\Services\PackageEntitlementService;
use App\Support\AppTimezone;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        if (! Schema::hasTable('consultation_requests')) {
            Log::warning('Consultations index requested but table is missing.', [
                'url' => $request->fullUrl(),
                'user_id' => auth()->id(),
            ]);

            $requests = new LengthAwarePaginator(
                collect(),
                0,
                25,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            $settings = (object) [
                'default_price' => 0,
                'default_duration_minutes' => 60,
                'payment_instructions' => null,
                'is_active' => false,
            ];

            $stats = [
                'new' => 0,
                'confirmed' => 0,
                'rescheduled' => 0,
                'completed' => 0,
                'cancelled' => 0,
                'pending' => 0,
                'payment_reported' => 0,
                'awaiting_verification' => 0,
                'paid' => 0,
                'scheduled' => 0,
            ];

            $status = (string) $request->get('status', 'all');
            $servicesCount = 0;
            return view('admin.consultations.index', compact('requests', 'settings', 'stats', 'status', 'servicesCount'))
                ->with('error', 'ميزة الاستشارات غير مفعلة حالياً لأن جدول البيانات غير موجود. يرجى تشغيل الترحيلات (migrations).');
        }

        $status = (string) $request->get('status', 'all');
        $query = ConsultationRequest::query()
            ->with(['instructor', 'student', 'classroomMeeting', 'service'])
            ->latest();

        if ($status === 'brief_new') {
            $query->whereIn('status', [
                ConsultationRequest::STATUS_NEW,
                ConsultationRequest::STATUS_PENDING,
                ConsultationRequest::STATUS_PAYMENT_REPORTED,
                ConsultationRequest::STATUS_AWAITING_VERIFICATION,
                ConsultationRequest::STATUS_PAID,
            ]);
        } elseif ($status === 'brief_confirmed') {
            $query->whereIn('status', [
                ConsultationRequest::STATUS_CONFIRMED,
                ConsultationRequest::STATUS_SCHEDULED,
            ]);
        } elseif (in_array($status, array_keys(ConsultationRequest::statusLabels()), true)) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(25)->withQueryString();
        $settings = ConsultationSetting::current();
        $servicesCount = Schema::hasTable('consultation_services')
            ? ConsultationService::query()->count()
            : 0;

        $stats = [
            'new' => ConsultationRequest::whereIn('status', [
                ConsultationRequest::STATUS_NEW,
                ConsultationRequest::STATUS_PENDING,
                ConsultationRequest::STATUS_PAYMENT_REPORTED,
                ConsultationRequest::STATUS_AWAITING_VERIFICATION,
                ConsultationRequest::STATUS_PAID,
            ])->count(),
            'confirmed' => ConsultationRequest::whereIn('status', [
                ConsultationRequest::STATUS_CONFIRMED,
                ConsultationRequest::STATUS_SCHEDULED,
            ])->count(),
            'rescheduled' => ConsultationRequest::where('status', ConsultationRequest::STATUS_RESCHEDULED)->count(),
            'completed' => ConsultationRequest::where('status', ConsultationRequest::STATUS_COMPLETED)->count(),
            'cancelled' => ConsultationRequest::where('status', ConsultationRequest::STATUS_CANCELLED)->count(),
            // legacy keys kept for old blade fragments
            'pending' => ConsultationRequest::where('status', ConsultationRequest::STATUS_PENDING)->count(),
            'payment_reported' => ConsultationRequest::where('status', ConsultationRequest::STATUS_PAYMENT_REPORTED)->count(),
            'awaiting_verification' => ConsultationRequest::where('status', ConsultationRequest::STATUS_AWAITING_VERIFICATION)->count(),
            'paid' => ConsultationRequest::where('status', ConsultationRequest::STATUS_PAID)->count(),
            'scheduled' => ConsultationRequest::whereIn('status', [
                ConsultationRequest::STATUS_SCHEDULED,
                ConsultationRequest::STATUS_CONFIRMED,
            ])->count(),
        ];

        return view('admin.consultations.index', compact('requests', 'settings', 'stats', 'status', 'servicesCount'));
    }

    public function updateSettings(Request $request)
    {
        $settings = ConsultationSetting::current();
        $data = $request->validate([
            'default_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'default_duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'payment_instructions' => ['nullable', 'string', 'max:20000'],
        ]);

        $settings->update([
            'default_price' => $data['default_price'],
            'default_duration_minutes' => $data['default_duration_minutes'],
            'payment_instructions' => $data['payment_instructions'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'تم حفظ إعدادات الاستشارات.');
    }

    public function show(ConsultationRequest $consultation)
    {
        $consultation->load([
            'instructor',
            'student',
            'classroomMeeting',
            'paidConfirmedBy',
            'platformWallet',
            'walletTransaction',
            'service',
            'order',
        ]);

        return view('admin.consultations.show', compact('consultation'));
    }

    public function confirmPayment(ConsultationRequest $consultation)
    {
        if (! in_array($consultation->status, [
            ConsultationRequest::STATUS_NEW,
            ConsultationRequest::STATUS_PENDING,
            ConsultationRequest::STATUS_PAYMENT_REPORTED,
            ConsultationRequest::STATUS_AWAITING_VERIFICATION,
        ], true)) {
            return back()->with('error', 'لا يمكن تأكيد الدفع لهذه الحالة.');
        }

        $viaWallet = $consultation->paidViaWallet();
        $viaPlatform = $consultation->paidViaPlatformAccounts();
        $msg = $viaWallet
            ? 'تم قبول طلب الاستشارة والدفع عبر محفظة الرصيد مع المدرب ' . ($consultation->instructor->name ?? '') . '. سيتم تأكيد الموعد من لوحة الإدارة.'
            : ($viaPlatform
                ? 'تم تأكيد استلام مبلغ الاستشارة (تحويل على حسابات المنصة) مع المدرب ' . ($consultation->instructor->name ?? '') . '. سيتم تأكيد الموعد قريباً.'
                : 'تم تأكيد استلام مبلغ الاستشارة مع المدرب ' . ($consultation->instructor->name ?? '') . '. سيتم تأكيد الموعد قريباً.');

        $consultation->update([
            'status' => ConsultationRequest::STATUS_PAID,
            'paid_confirmed_at' => now(),
            'paid_confirmed_by' => auth()->id(),
        ]);

        if ($consultation->order_id) {
            $linkedOrder = \App\Models\Order::query()->find($consultation->order_id);
            if ($linkedOrder && $linkedOrder->status === \App\Models\Order::STATUS_PENDING) {
                $linkedOrder->update([
                    'status' => \App\Models\Order::STATUS_APPROVED,
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'notes' => trim((string) ($linkedOrder->notes ?? '')."\n[consultation_confirm_payment #{$consultation->id}]"),
                ]);
            }
        }

        Notification::create([
            'user_id' => $consultation->student_id,
            'sender_id' => auth()->id(),
            'title' => $viaWallet ? 'تم قبول طلب الاستشارة' : 'تم تأكيد دفع الاستشارة',
            'message' => $msg,
            'type' => 'general',
            'priority' => 'normal',
            'audience' => 'student',
            'action_url' => route('consultations.show', $consultation),
            'action_text' => 'تفاصيل الطلب',
        ]);

        return back()->with('success', 'تم تأكيد الدفع — أكّد الموعد ليصبح Confirmed.');
    }

    public function schedule(Request $request, ConsultationRequest $consultation)
    {
        if (! in_array($consultation->status, [
            ConsultationRequest::STATUS_NEW,
            ConsultationRequest::STATUS_PAID,
            ConsultationRequest::STATUS_PAYMENT_REPORTED,
            ConsultationRequest::STATUS_AWAITING_VERIFICATION,
            ConsultationRequest::STATUS_PENDING,
        ], true)) {
            return back()->with('error', 'لا يمكن تأكيد الموعد لهذه الحالة.');
        }

        $data = $request->validate([
            'scheduled_at' => ['required', 'date'],
            'timezone' => AppTimezone::inputRules(),
            'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:480'],
        ]);
        $data = AppTimezone::shiftRequestDateTime(
            $request,
            $data,
            'scheduled_at',
            mustBeFuture: true,
            fallbackUser: $consultation->instructor
        );

        $scheduledAt = $data['scheduled_at'];
        $duration = (int) ($data['duration_minutes'] ?? $consultation->duration_minutes);

        if ($consultation->instructor_id) {
            $endsAt = $scheduledAt->copy()->addMinutes($duration);
            if (OneToOneAvailabilityService::hasConflict(
                (int) $consultation->instructor_id,
                $scheduledAt,
                $endsAt,
                null,
                null
            )) {
                return back()->with('error', 'الموعد يتعارض مع حجز آخر لنفس المدرب. اختر وقتًا آخر.')->withInput();
            }
        }

        $meetingPayload = [
            'user_id' => $consultation->instructor_id,
            'consultation_request_id' => $consultation->id,
            'code' => ClassroomMeeting::generateCode(),
            'room_name' => 'consultation-' . $consultation->id . '-' . Str::lower(Str::random(6)),
            'title' => $consultation->service?->title_ar ?: 'استشارة',
            'scheduled_for' => $scheduledAt,
            'planned_duration_minutes' => $duration,
            'settings' => [
                'allow_guest_join' => false,
                'consultation' => true,
                'max_participants' => 12,
            ],
        ];
        if (Schema::hasColumn('classroom_meetings', 'max_participants')) {
            $meetingPayload['max_participants'] = 12;
        }

        $meeting = ClassroomMeeting::create($meetingPayload);

        $updates = [
            'status' => ConsultationRequest::STATUS_CONFIRMED,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $duration,
            'classroom_meeting_id' => $meeting->id,
        ];
        if (! $consultation->paid_confirmed_at) {
            $updates['paid_confirmed_at'] = now();
            $updates['paid_confirmed_by'] = auth()->id();
        }

        $consultation->update($updates);
        $consultation->refresh()->load(['student', 'instructor', 'service', 'classroomMeeting']);

        ConsultationNotificationService::notifyBookingConfirmed($consultation, auth()->user());

        return back()->with('success', 'تم تأكيد الحجز (Confirmed) وإرسال التفاصيل عبر الإشعار / Email / WhatsApp.');
    }

    public function reschedule(Request $request, ConsultationRequest $consultation)
    {
        if (! $consultation->isActiveBooking()) {
            return back()->with('error', 'إعادة الجدولة للحجوزات المؤكدة فقط.');
        }

        $data = $request->validate([
            'scheduled_at' => ['required', 'date'],
            'timezone' => AppTimezone::inputRules(),
            'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:480'],
        ]);
        $data = AppTimezone::shiftRequestDateTime(
            $request,
            $data,
            'scheduled_at',
            mustBeFuture: true,
            fallbackUser: $consultation->instructor
        );

        $scheduledAt = $data['scheduled_at'];
        $duration = (int) ($data['duration_minutes'] ?? $consultation->duration_minutes);
        $previous = $consultation->scheduled_at;

        if ($consultation->instructor_id) {
            $endsAt = $scheduledAt->copy()->addMinutes($duration);
            if (OneToOneAvailabilityService::hasConflict(
                (int) $consultation->instructor_id,
                $scheduledAt,
                $endsAt,
                null,
                (int) $consultation->id
            )) {
                return back()->with('error', 'الموعد الجديد يتعارض مع حجز آخر لنفس المدرب.')->withInput();
            }
        }

        if ($consultation->classroomMeeting) {
            $consultation->classroomMeeting->update([
                'scheduled_for' => $scheduledAt,
                'planned_duration_minutes' => $duration,
            ]);
        } else {
            $meetingPayload = [
                'user_id' => $consultation->instructor_id,
                'consultation_request_id' => $consultation->id,
                'code' => ClassroomMeeting::generateCode(),
                'room_name' => 'consultation-' . $consultation->id . '-' . Str::lower(Str::random(6)),
                'title' => $consultation->service?->title_ar ?: 'استشارة',
                'scheduled_for' => $scheduledAt,
                'planned_duration_minutes' => $duration,
                'settings' => ['allow_guest_join' => false, 'consultation' => true, 'max_participants' => 12],
            ];
            if (Schema::hasColumn('classroom_meetings', 'max_participants')) {
                $meetingPayload['max_participants'] = 12;
            }
            $meeting = ClassroomMeeting::create($meetingPayload);
            $consultation->classroom_meeting_id = $meeting->id;
        }

        $consultation->update([
            'status' => ConsultationRequest::STATUS_RESCHEDULED,
            'rescheduled_from' => $previous,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $duration,
            'classroom_meeting_id' => $consultation->classroom_meeting_id,
            'reminder_sent_at' => null,
        ]);

        $consultation->refresh()->load(['student', 'instructor', 'service', 'classroomMeeting']);
        ConsultationNotificationService::notifyRescheduled($consultation, auth()->user());

        return back()->with('success', 'تم إعادة الجدولة (Rescheduled) وإشعار الأطراف.');
    }

    public function updateNotes(Request $request, ConsultationRequest $consultation)
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:20000'],
        ]);
        $consultation->update(['admin_notes' => $data['admin_notes'] ?? null]);

        return back()->with('success', 'تم حفظ الملاحظات.');
    }

    public function updateOutcome(Request $request, ConsultationRequest $consultation)
    {
        $data = $request->validate([
            'outcome_notes' => ['nullable', 'string', 'max:20000'],
            'recommendations' => ['nullable', 'string', 'max:20000'],
        ]);

        $consultation->update([
            'outcome_notes' => $data['outcome_notes'] ?? null,
            'recommendations' => $data['recommendations'] ?? null,
        ]);

        return back()->with('success', 'تم حفظ نتيجة الجلسة والتوصيات.');
    }

    public function cancel(ConsultationRequest $consultation)
    {
        if (in_array($consultation->status, [ConsultationRequest::STATUS_COMPLETED, ConsultationRequest::STATUS_CANCELLED], true)) {
            return back()->with('error', 'لا يمكن إلغاء هذا الطلب.');
        }

        $refundedToWallet = false;

        DB::transaction(function () use ($consultation, &$refundedToWallet) {
            if ($consultation->status === ConsultationRequest::STATUS_AWAITING_VERIFICATION
                && $consultation->wallet_transaction_id) {
                $tx = WalletTransaction::with('wallet')->find($consultation->wallet_transaction_id);
                if ($tx && $tx->wallet && (int) $tx->wallet->user_id === (int) $consultation->student_id) {
                    $tx->wallet->deposit(
                        (float) $consultation->price_amount,
                        null,
                        null,
                        'استرجاع — إلغاء طلب استشارة #'.$consultation->id
                    );
                    $refundedToWallet = true;
                }
            }

            PackageEntitlementService::restoreConsultationSessionFromReference($consultation->payment_reference);

            if ($consultation->order_id) {
                $linkedOrder = \App\Models\Order::query()->find($consultation->order_id);
                if ($linkedOrder && $linkedOrder->status === \App\Models\Order::STATUS_PENDING) {
                    $linkedOrder->update([
                        'status' => \App\Models\Order::STATUS_REJECTED,
                        'notes' => trim((string) ($linkedOrder->notes ?? '')."\n[consultation_cancelled #{$consultation->id}]"),
                    ]);
                }
            }

            $consultation->update(['status' => ConsultationRequest::STATUS_CANCELLED]);
        });

        $extra = $refundedToWallet ? 'وعُيد المبلغ إلى محفظتك.' : '';
        ConsultationNotificationService::notifyCancelled($consultation, $extra, auth()->user());

        return back()->with('success', 'تم إلغاء الطلب (Cancelled).'.($refundedToWallet ? ' وتم استرجاع المبلغ لمحفظة الطالب.' : ''));
    }

    public function markCompleted(ConsultationRequest $consultation)
    {
        if (! in_array($consultation->status, [
            ConsultationRequest::STATUS_CONFIRMED,
            ConsultationRequest::STATUS_RESCHEDULED,
            ConsultationRequest::STATUS_SCHEDULED,
        ], true)) {
            return back()->with('error', 'يمكن الإكمال للحجوزات المؤكدة فقط.');
        }

        DB::transaction(function () use ($consultation) {
            $consultation->update(['status' => ConsultationRequest::STATUS_COMPLETED]);

            $agreement = InstructorAgreement::query()
                ->where('instructor_id', $consultation->instructor_id)
                ->where('status', InstructorAgreement::STATUS_ACTIVE)
                ->where(function ($q) use ($consultation) {
                    $q->whereNull('start_date')
                        ->orWhereDate('start_date', '<=', $consultation->scheduled_at ?? now());
                })
                ->where(function ($q) use ($consultation) {
                    $q->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $consultation->scheduled_at ?? now());
                })
                ->where(function ($q) {
                    $q->where('type', 'consultation_session')
                        ->orWhere('billing_type', InstructorAgreement::BILLING_CONSULTATION);
                })
                ->latest('start_date')
                ->latest('id')
                ->first();

            if (! $agreement || (float) $agreement->rate <= 0) {
                return;
            }

            $existing = AgreementPayment::query()
                ->where('agreement_id', $agreement->id)
                ->where('instructor_id', $consultation->instructor_id)
                ->where('type', AgreementPayment::TYPE_CONSULTATION_SESSION)
                ->where('description', 'like', '%#'.$consultation->id.'%')
                ->exists();

            if ($existing) {
                return;
            }

            AgreementPayment::create([
                'agreement_id' => $agreement->id,
                'instructor_id' => $consultation->instructor_id,
                'type' => AgreementPayment::TYPE_CONSULTATION_SESSION,
                'amount' => (float) $agreement->rate,
                'status' => AgreementPayment::STATUS_APPROVED,
                'description' => 'مستحق استشارة مكتملة #'.$consultation->id.' — المعلم: '.($consultation->student->name ?? '—'),
                'payment_date' => now(),
                'created_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'تم تسجيل الاستشارة كمكتملة (Completed). سجّل النتيجة/التوصيات إن لزم.');
    }
}
