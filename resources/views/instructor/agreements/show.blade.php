@extends('layouts.instructor-timeline')

@section('title', __('instructor.agreement_details_title') . ' - ' . config('app.name'))
@section('page_title', __('instructor.agreement_details_title'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $stChip = match ($agreement->status) {
        'active' => 'su-chip--ok',
        'draft' => '',
        'suspended' => 'su-chip--warn',
        'terminated' => 'su-chip--off',
        default => 'su-soft-1',
    };
    $stLabel = match ($agreement->status) {
        'active' => __('instructor.active_status'),
        'draft' => __('instructor.draft'),
        'suspended' => __('instructor.suspended'),
        'terminated' => __('instructor.terminated'),
        default => __('instructor.agreement_completed'),
    };
@endphp
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.agreements.index') }}">{{ __('instructor.agreements_system') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ Str::limit($agreement->title, 40) }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-file-contract su-page-head__ico" aria-hidden="true"></i>
                {{ $agreement->title }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.agreement_number') }}: {{ $agreement->agreement_number ?? 'N/A' }}</p>
            <div class="su-chip-row">
                <span class="su-chip {{ $stChip }}">{{ $stLabel }}</span>
            </div>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.agreements.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.total_payments') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['total_earned'], 2) }}</div>
                <div class="su-kpi__d"><i class="fas fa-sack-dollar" aria-hidden="true"></i></div>
            </div>
            <div style="font-size:12px;color:var(--su-ink-40)">{{ __('public.currency_egp') }}</div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.pending') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['pending_amount'], 2) }}</div>
                <div class="su-kpi__d"><i class="fas fa-clock" aria-hidden="true"></i></div>
            </div>
            <div style="font-size:12px;color:var(--su-ink-40)">{{ __('public.currency_egp') }}</div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.total_payments') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ $stats['total_payments'] }}</div>
                <div class="su-kpi__d"><i class="fas fa-receipt" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.paid') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ $stats['paid_payments'] }}</div>
                <div class="su-kpi__d"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <div class="su-detail-grid">
        <div style="display:flex;flex-direction:column;gap:16px;min-width:0">
            <section class="su-card">
                <h2 class="su-card__title"><i class="fas fa-info-circle" aria-hidden="true"></i> {{ __('instructor.agreement_info') }}</h2>
                <div class="su-meta-list">
                    <div class="su-meta-row">
                        <span class="su-meta-ico su-soft-1"><i class="fas fa-tag" aria-hidden="true"></i></span>
                        <span>{{ __('instructor.agreement_type_label') }}:</span>
                        <strong>
                            @if(($agreement->billing_type ?? '') === 'course_percentage')
                                {{ __('instructor.course_percentage_type') }}
                                @if($agreement->advancedCourse)
                                    <span style="display:block;font-weight:400;font-size:12px;color:var(--su-ink-40)">{{ $agreement->advancedCourse->title }}</span>
                                @endif
                            @elseif($agreement->type == 'course_price')
                                {{ __('instructor.course_price_full') }}
                            @elseif($agreement->type == 'hourly_rate')
                                {{ __('instructor.hourly_rate_recorded') }}
                            @elseif($agreement->type == 'consultation_session')
                                {{ __('instructor.consultations_type') }}
                            @else
                                {{ __('instructor.monthly_salary') }}
                            @endif
                        </strong>
                    </div>
                    <div class="su-meta-row">
                        <span class="su-meta-ico su-soft-2"><i class="fas fa-percent" aria-hidden="true"></i></span>
                        <span>{{ (($agreement->billing_type ?? '') === 'course_percentage') ? __('instructor.instructor_share_pct') : __('instructor.rate') }}:</span>
                        <strong class="tabular-nums">
                            @if(($agreement->billing_type ?? '') === 'course_percentage')
                                {{ number_format($agreement->course_percentage ?? 0, 2) }}%
                            @else
                                {{ number_format($agreement->rate, 2) }} {{ __('public.currency_egp') }}
                            @endif
                        </strong>
                    </div>
                    <div class="su-meta-row">
                        <span class="su-meta-ico su-soft-3"><i class="fas fa-info" aria-hidden="true"></i></span>
                        <span>{{ __('common.status') }}:</span>
                        <span class="su-chip {{ $stChip }}">{{ $stLabel }}</span>
                    </div>
                    <div class="su-meta-row">
                        <span class="su-meta-ico su-soft-4"><i class="fas fa-calendar" aria-hidden="true"></i></span>
                        <span>{{ __('instructor.start_date') }}:</span>
                        <strong>{{ $agreement->start_date ? $agreement->start_date->format('Y-m-d') : '-' }}</strong>
                    </div>
                    @if($agreement->end_date)
                        <div class="su-meta-row">
                            <span class="su-meta-ico su-soft-1"><i class="fas fa-calendar-check" aria-hidden="true"></i></span>
                            <span>{{ __('instructor.end_date') }}:</span>
                            <strong>{{ $agreement->end_date->format('Y-m-d') }}</strong>
                        </div>
                    @endif
                </div>
                @if($agreement->description)
                    <div style="margin-top:16px">
                        <h3 style="font-size:13px;font-weight:600;margin:0 0 6px">{{ __('instructor.description') }}</h3>
                        <p style="margin:0;font-size:14px;color:var(--su-ink-40);line-height:1.6">{{ $agreement->description }}</p>
                    </div>
                @endif
                @if($agreement->terms)
                    <div style="margin-top:16px">
                        <h3 style="font-size:13px;font-weight:600;margin:0 0 6px">{{ __('instructor.contract_terms') }}</h3>
                        <div style="font-size:14px;color:var(--su-ink-40);white-space:pre-line;line-height:1.6">{{ $agreement->terms }}</div>
                    </div>
                @endif
                @if($agreement->notes)
                    <div style="margin-top:16px;padding:12px;border-radius:10px;background:rgba(245,158,11,.08)">
                        <h3 style="font-size:13px;font-weight:600;margin:0 0 6px">{{ __('instructor.notes') }}</h3>
                        <div style="font-size:14px;white-space:pre-line;line-height:1.6">{{ $agreement->notes }}</div>
                    </div>
                @endif
            </section>

            @if(($agreement->billing_type ?? '') === 'course_percentage')
                @php $activationPayments = $agreement->payments->where('type', 'course_activation'); @endphp
                <section class="su-card su-card--flush">
                    <div class="su-section-head" style="padding:14px 16px;border-bottom:1px solid var(--su-line,rgba(0,0,0,.06))">
                        <div>
                            <h2 class="su-card__title" style="margin:0">
                                <i class="fas fa-user-graduate" aria-hidden="true"></i>
                                {{ __('instructor.student_activations_share') }}
                            </h2>
                            <p style="margin:4px 0 0;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.student_activations_desc') }}</p>
                        </div>
                        <a href="{{ route('instructor.agreements.export-activations', $agreement) }}" class="su-btn su-btn--primary" style="height:32px">
                            <i class="fas fa-file-excel" aria-hidden="true"></i>
                            {{ __('instructor.export_excel') }}
                        </a>
                    </div>
                    @if($activationPayments->isNotEmpty())
                        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
                            <table class="su-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('common.date') }}</th>
                                        <th>{{ __('instructor.student') }}</th>
                                        <th>{{ __('instructor.purchase_amount') }}</th>
                                        <th>{{ __('instructor.my_percentage') }}</th>
                                        <th>{{ __('instructor.my_share') }}</th>
                                        <th>{{ __('common.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activationPayments as $p)
                                        <tr>
                                            <td class="tabular-nums" style="color:var(--su-ink-40)">{{ $p->created_at?->format('Y-m-d') ?? '—' }}</td>
                                            <td>{{ $p->enrollment?->student?->name ?? '—' }}</td>
                                            <td class="tabular-nums">{{ $p->enrollment ? number_format($p->enrollment->final_price ?? 0, 2) : '—' }}</td>
                                            <td class="tabular-nums">{{ number_format($agreement->course_percentage ?? 0, 2) }}%</td>
                                            <td class="tabular-nums"><strong>{{ number_format($p->amount, 2) }} {{ __('public.currency_egp') }}</strong></td>
                                            <td>
                                                @if($p->status === 'paid')
                                                    <span class="su-chip su-chip--ok">{{ __('instructor.paid') }}</span>
                                                @elseif($p->status === 'approved')
                                                    <span class="su-chip su-chip--warn">{{ __('instructor.approved') }}</span>
                                                @else
                                                    <span class="su-chip">{{ __('instructor.pending_review') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div style="padding:12px 16px;border-top:1px solid var(--su-line,rgba(0,0,0,.06));font-weight:600">
                            {{ __('instructor.total_earnings_from_agreement') }}:
                            <span class="tabular-nums">{{ number_format($activationPayments->sum('amount'), 2) }} {{ __('public.currency_egp') }}</span>
                        </div>
                    @else
                        <div class="su-empty" style="padding:40px 16px">
                            <i class="fas fa-user-graduate" aria-hidden="true"></i>
                            <p>{{ __('instructor.no_activations_yet') }}</p>
                            <p style="color:var(--su-ink-40);font-size:13px;margin:0">{{ __('instructor.no_activations_desc') }}</p>
                        </div>
                    @endif
                </section>
            @endif

            <section class="su-card su-card--flush">
                <div class="su-section-head" style="padding:14px 16px;border-bottom:1px solid var(--su-line,rgba(0,0,0,.06))">
                    <h2 class="su-card__title" style="margin:0">
                        <i class="fas fa-receipt" aria-hidden="true"></i>
                        {{ __('instructor.payments_log') }}
                    </h2>
                </div>
                <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
                    <table class="su-table">
                        <thead>
                            <tr>
                                <th>{{ __('instructor.payment_number') }}</th>
                                <th>{{ __('instructor.type') }}</th>
                                <th>{{ __('instructor.amount') }}</th>
                                <th>{{ __('common.status') }}</th>
                                <th>{{ __('common.date') }}</th>
                                <th>{{ __('instructor.transfer_receipt') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agreement->payments as $payment)
                                @php
                                    $typeLabels = [
                                        'course_completion' => __('instructor.course_price_full'),
                                        'course_sale' => __('instructor.course_price'),
                                        'course_price' => __('instructor.course_price'),
                                        'hourly_teaching' => __('instructor.hourly_rate_recorded'),
                                        'lecture_hour' => __('instructor.hourly_rate_recorded'),
                                        'hourly_rate' => __('instructor.hourly_rate'),
                                        'monthly_salary' => __('instructor.monthly_salary'),
                                        'consultation_session' => __('instructor.consultations_type'),
                                        'bonus' => __('instructor.bonus'),
                                        'other' => __('instructor.other'),
                                        'course_activation' => __('instructor.activation_share_type'),
                                    ];
                                    $typeLabel = $typeLabels[$payment->type] ?? ($payment->type ?? __('instructor.not_specified'));
                                    $pChip = match ($payment->status) {
                                        'paid' => 'su-chip--ok',
                                        'approved' => 'su-chip--warn',
                                        default => '',
                                    };
                                    $pLabel = match ($payment->status) {
                                        'paid' => __('instructor.received'),
                                        'approved' => __('instructor.approved'),
                                        default => __('instructor.pending_review'),
                                    };
                                @endphp
                                <tr>
                                    <td><strong>{{ $payment->payment_number ?? 'N/A' }}</strong></td>
                                    <td>
                                        <span class="su-chip su-soft-1">{{ $typeLabel }}</span>
                                        @if($payment->type === 'course_activation' && $payment->enrollment)
                                            <div style="font-size:12px;color:var(--su-ink-40);margin-top:4px">
                                                {{ __('instructor.student') }}: {{ $payment->enrollment->student->name ?? '—' }}
                                            </div>
                                            <div style="font-size:12px;color:var(--su-ink-40)">
                                                {{ __('instructor.activation_amount_share', [
                                                    'price' => number_format($payment->enrollment->final_price ?? 0, 2),
                                                    'share' => number_format($payment->amount, 2),
                                                ]) }}
                                            </div>
                                        @endif
                                        @if($payment->course)
                                            <div style="font-size:12px;color:var(--su-ink-40);margin-top:2px">{{ $payment->course->title ?? '' }}</div>
                                        @endif
                                        @if($payment->lecture)
                                            <div style="font-size:12px;color:var(--su-ink-40);margin-top:2px">{{ $payment->lecture->title ?? '' }}</div>
                                        @endif
                                        @if($payment->hours_count)
                                            <div style="font-size:12px;color:var(--su-ink-40);margin-top:2px">{{ $payment->hours_count }} {{ __('instructor.hour') }}</div>
                                        @endif
                                    </td>
                                    <td class="tabular-nums"><strong>{{ number_format($payment->amount, 2) }} {{ __('public.currency_egp') }}</strong></td>
                                    <td>
                                        <span class="su-chip {{ $pChip }}">{{ $pLabel }}</span>
                                        @if($payment->status == 'paid')
                                            <div style="font-size:11px;color:#16a34a;margin-top:4px">{{ __('instructor.amount_transferred_info') }}</div>
                                        @endif
                                    </td>
                                    <td class="tabular-nums" style="color:var(--su-ink-40)">{{ $payment->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @if($payment->status == 'paid' && $payment->transfer_receipt_path)
                                            <a href="{{ storage_asset($payment->transfer_receipt_path) }}" target="_blank" rel="noopener" class="su-btn" style="height:28px;font-size:12px">
                                                <i class="fas fa-receipt" aria-hidden="true"></i>
                                                {{ __('instructor.download_receipt') }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="su-empty">
                                            <i class="fas fa-receipt" aria-hidden="true"></i>
                                            <p>{{ __('instructor.no_payments') }}</p>
                                            <p style="color:var(--su-ink-40);font-size:13px;margin:0">{{ __('instructor.no_payments_yet') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div>
            <section class="su-card">
                <h2 class="su-card__title"><i class="fas fa-lightbulb" aria-hidden="true"></i> {{ __('instructor.quick_tips') }}</h2>
                <ul style="margin:0;padding-inline-start:1.1rem;font-size:13px;color:var(--su-ink-40);line-height:1.7">
                    <li>{{ __('instructor.tips_follow_payments') }}</li>
                    <li>{{ __('instructor.tips_pending') }}</li>
                    <li>{{ __('instructor.tips_completed') }}</li>
                </ul>
            </section>
        </div>
    </div>
</div>
@endsection
