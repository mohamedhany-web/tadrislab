@extends('layouts.instructor-timeline')

@section('title', __('instructor.agreements_system') . ' - ' . config('app.name'))
@section('page_title', __('instructor.agreements_system'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-handshake su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.agreements_system') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.agreements_subtitle') }}</p>
        </div>
    </div>

    <section class="su-kpi-row su-kpi-row--3" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.total_earned') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['total_earned'], 2) }}</div>
                <div class="su-kpi__d"><i class="fas fa-sack-dollar" aria-hidden="true"></i></div>
            </div>
            <div style="font-size:12px;color:var(--su-ink-40);margin-top:4px">{{ __('public.currency_egp') }}</div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.pending') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['pending_amount'], 2) }}</div>
                <div class="su-kpi__d"><i class="fas fa-clock" aria-hidden="true"></i></div>
            </div>
            <div style="font-size:12px;color:var(--su-ink-40);margin-top:4px">{{ __('public.currency_egp') }}</div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.total_payments') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['total_payments']) }}</div>
                <div class="su-kpi__d"><i class="fas fa-receipt" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    @if($activeAgreement)
        <section class="su-card" style="margin-bottom:20px;border-color:rgba(34,197,94,.3);background:rgba(34,197,94,.06)">
            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px">
                <div>
                    <div class="su-chip-row" style="margin-bottom:8px">
                        <span class="su-chip su-chip--ok">{{ __('instructor.active_status') }}</span>
                        <strong style="font-size:16px">{{ $activeAgreement->title }}</strong>
                    </div>
                    <div class="su-meta-list">
                        <div class="su-meta-row">
                            <span>{{ __('instructor.agreement_number') }}:</span>
                            <strong>{{ $activeAgreement->agreement_number }}</strong>
                        </div>
                        <div class="su-meta-row">
                            <span>{{ __('instructor.type') }}:</span>
                            <strong>
                                @if($activeAgreement->type == 'course_price') {{ __('instructor.course_price') }}
                                @elseif($activeAgreement->type == 'hourly_rate') {{ __('instructor.hourly_rate') }}
                                @else {{ __('instructor.monthly_salary') }}
                                @endif
                            </strong>
                        </div>
                        <div class="su-meta-row">
                            <span>{{ __('instructor.rate') }}:</span>
                            <strong class="tabular-nums">{{ number_format($activeAgreement->rate, 2) }} {{ __('public.currency_egp') }}</strong>
                        </div>
                    </div>
                </div>
                <a href="{{ route('instructor.agreements.show', $activeAgreement) }}" class="su-btn su-btn--primary">
                    <i class="fas fa-eye" aria-hidden="true"></i>
                    {{ __('instructor.view_details') }}
                </a>
            </div>
        </section>
    @endif

    <section class="su-card su-card--flush">
        <div class="su-section-head" style="padding:14px 16px;border-bottom:1px solid var(--su-line,rgba(0,0,0,.06))">
            <h2 class="su-card__title" style="margin:0">
                <i class="fas fa-handshake" aria-hidden="true"></i>
                {{ __('instructor.all_agreements') }}
            </h2>
        </div>
        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
            <table class="su-table">
                <thead>
                    <tr>
                        <th>{{ __('instructor.agreement_number') }}</th>
                        <th>{{ __('instructor.title') }}</th>
                        <th>{{ __('instructor.type') }}</th>
                        <th>{{ __('instructor.rate') }}</th>
                        <th>{{ __('common.status') }}</th>
                        <th>{{ __('instructor.start_date') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agreements as $agreement)
                        @php
                            $typeChip = match ($agreement->type) {
                                'course_price' => 'su-soft-1',
                                'hourly_rate' => 'su-soft-2',
                                default => 'su-soft-3',
                            };
                            $typeLabel = match ($agreement->type) {
                                'course_price' => __('instructor.course_price'),
                                'hourly_rate' => __('instructor.hourly_rate'),
                                default => __('instructor.monthly_salary'),
                            };
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
                        <tr>
                            <td><strong>{{ $agreement->agreement_number }}</strong></td>
                            <td>{{ $agreement->title }}</td>
                            <td><span class="su-chip {{ $typeChip }}">{{ $typeLabel }}</span></td>
                            <td class="tabular-nums">{{ number_format($agreement->rate, 2) }} {{ __('public.currency_egp') }}</td>
                            <td><span class="su-chip {{ $stChip }}">{{ $stLabel }}</span></td>
                            <td class="tabular-nums" style="color:var(--su-ink-40)">{{ $agreement->start_date->format('Y-m-d') }}</td>
                            <td style="text-align:end">
                                <a href="{{ route('instructor.agreements.show', $agreement) }}" class="su-btn" style="height:32px">
                                    {{ __('common.view') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="su-empty">
                                    <i class="fas fa-handshake" aria-hidden="true"></i>
                                    <p>{{ __('instructor.no_agreements') }}</p>
                                    <p style="color:var(--su-ink-40);font-size:13px;margin:0">{{ __('instructor.no_agreements_description') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
