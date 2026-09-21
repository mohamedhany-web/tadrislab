@extends('layouts.instructor-timeline')

@section('title', __('instructor.withdrawal_requests') . ' - ' . config('app.name'))
@section('page_title', __('instructor.withdrawal_requests'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-money-check-dollar su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.withdrawal_requests') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.withdrawals_subtitle') }}</p>
        </div>
        <div class="su-page-head__actions">
            @if($stats['available_amount'] > 0)
                <a href="{{ route('instructor.withdrawals.create') }}" class="su-btn su-btn--primary">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    {{ __('instructor.new_withdrawal_request') }}
                </a>
            @endif
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.total_earned') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['total_earned'], 2) }}</div>
                <div class="su-kpi__d"><i class="fas fa-sack-dollar" aria-hidden="true"></i></div>
            </div>
            <div style="font-size:12px;color:var(--su-ink-40)">{{ __('public.currency_egp') }}</div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.total_withdrawn') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['total_withdrawn'], 2) }}</div>
                <div class="su-kpi__d"><i class="fas fa-arrow-down" aria-hidden="true"></i></div>
            </div>
            <div style="font-size:12px;color:var(--su-ink-40)">{{ __('public.currency_egp') }}</div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.pending_withdrawals') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['pending_withdrawals'], 2) }}</div>
                <div class="su-kpi__d"><i class="fas fa-clock" aria-hidden="true"></i></div>
            </div>
            <div style="font-size:12px;color:var(--su-ink-40)">{{ __('public.currency_egp') }}</div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.available_amount') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['available_amount'], 2) }}</div>
                <div class="su-kpi__d"><i class="fas fa-wallet" aria-hidden="true"></i></div>
            </div>
            <div style="font-size:12px;color:var(--su-ink-40)">{{ __('public.currency_egp') }}</div>
        </div>
    </section>

    <section class="su-card su-card--flush">
        <div class="su-section-head" style="padding:14px 16px;border-bottom:1px solid var(--su-line,rgba(0,0,0,.06))">
            <h2 class="su-card__title" style="margin:0">
                <i class="fas fa-money-check-dollar" aria-hidden="true"></i>
                {{ __('instructor.withdrawal_requests') }}
            </h2>
        </div>
        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
            <table class="su-table">
                <thead>
                    <tr>
                        <th>{{ __('instructor.request_number') }}</th>
                        <th>{{ __('instructor.amount') }}</th>
                        <th>{{ __('instructor.payment_method') }}</th>
                        <th>{{ __('common.status') }}</th>
                        <th>{{ __('instructor.request_date') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                        @php
                            $methodLabel = match ($withdrawal->payment_method) {
                                'bank_transfer' => __('instructor.bank_transfer'),
                                'wallet' => __('instructor.wallet'),
                                'cash' => __('instructor.cash'),
                                default => __('instructor.other'),
                            };
                            $stChip = match ($withdrawal->status) {
                                'completed' => 'su-chip--ok',
                                'processing' => 'su-soft-1',
                                'approved' => 'su-chip--warn',
                                'pending' => '',
                                'rejected', 'cancelled' => 'su-chip--off',
                                default => '',
                            };
                            $stLabel = match ($withdrawal->status) {
                                'completed' => __('instructor.completed'),
                                'processing' => __('instructor.processing'),
                                'approved' => __('instructor.approved'),
                                'pending' => __('instructor.pending_status'),
                                'rejected' => __('instructor.rejected'),
                                'cancelled' => __('instructor.cancelled'),
                                default => $withdrawal->status,
                            };
                        @endphp
                        <tr>
                            <td><strong>{{ $withdrawal->request_number ?? '#' . $withdrawal->id }}</strong></td>
                            <td class="tabular-nums"><strong>{{ number_format($withdrawal->amount, 2) }} {{ __('public.currency_egp') }}</strong></td>
                            <td><span class="su-chip su-soft-1">{{ $methodLabel }}</span></td>
                            <td><span class="su-chip {{ $stChip }}">{{ $stLabel }}</span></td>
                            <td class="tabular-nums" style="color:var(--su-ink-40)">{{ $withdrawal->created_at->format('Y-m-d H:i') }}</td>
                            <td style="text-align:end">
                                <div style="display:inline-flex;gap:6px">
                                    <a href="{{ route('instructor.withdrawals.show', $withdrawal) }}" class="su-btn" style="height:32px;width:32px;padding:0;justify-content:center" title="{{ __('common.view') }}">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </a>
                                    @if(in_array($withdrawal->status, ['pending', 'approved']))
                                        <form action="{{ route('instructor.withdrawals.cancel', $withdrawal) }}" method="POST"
                                              onsubmit="return confirm(@json(__('instructor.confirm_cancel_withdrawal')));" style="display:inline">
                                            @csrf
                                            <button type="submit" class="su-btn" style="height:32px;width:32px;padding:0;justify-content:center;color:#b91c1c" title="{{ __('instructor.cancel') }}">
                                                <i class="fas fa-times" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="su-empty">
                                    <i class="fas fa-money-bill-wave" aria-hidden="true"></i>
                                    <p>{{ __('instructor.no_withdrawals') }}</p>
                                    <p style="color:var(--su-ink-40);font-size:13px;margin:0">{{ __('instructor.no_withdrawals_description') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($withdrawals->hasPages())
            <div class="su-pager" style="padding:12px">{{ $withdrawals->links() }}</div>
        @endif
    </section>
</div>
@endsection
