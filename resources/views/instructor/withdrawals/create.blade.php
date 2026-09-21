@extends('layouts.instructor-timeline')

@section('title', __('instructor.new_withdrawal_request') . ' - ' . config('app.name'))
@section('page_title', __('instructor.new_withdrawal_request'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page" style="max-width:48rem">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.withdrawals.index') }}">{{ __('instructor.withdrawal_requests') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.new_withdrawal_request') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-plus-circle su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.submit_withdrawal') }}
            </h1>
            <p class="su-page-head__sub">
                {{ __('instructor.available_for_withdrawal') }}:
                <strong class="tabular-nums">{{ number_format($stats['available_amount'], 2) }} {{ __('public.currency_egp') }}</strong>
            </p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.withdrawals.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.total_earned') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['total_earned'], 2) }}</div>
                <div class="su-kpi__d"><i class="fas fa-money-bill-wave" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.total_withdrawn') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['total_withdrawn'], 2) }}</div>
                <div class="su-kpi__d"><i class="fas fa-arrow-down" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.pending_withdrawals') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['pending_withdrawals'], 2) }}</div>
                <div class="su-kpi__d"><i class="fas fa-clock" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.available_amount') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v tabular-nums">{{ number_format($stats['available_amount'], 2) }}</div>
                <div class="su-kpi__d"><i class="fas fa-wallet" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <section class="su-card">
        @if(session('error'))
            <div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;border:1px solid rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#b91c1c;font-size:13px">
                {{ session('error') }}
            </div>
        @endif

        @if($stats['available_amount'] <= 0)
            <div class="su-empty">
                <i class="fas fa-wallet" aria-hidden="true"></i>
                <p>{{ __('instructor.no_available_amount') }}</p>
                <p style="color:var(--su-ink-40);font-size:13px;margin:0 0 12px">{{ __('instructor.no_available_amount_desc') }}</p>
                <a href="{{ route('instructor.withdrawals.index') }}" class="su-btn">
                    <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                    {{ __('instructor.back') }}
                </a>
            </div>
        @else
            <form action="{{ route('instructor.withdrawals.store') }}" method="POST">
                @csrf
                <div class="su-form-grid" style="grid-template-columns:1fr">
                    <div class="su-field">
                        <label for="amount">{{ __('instructor.amount_required_egp') }} <span style="color:#b91c1c">*</span></label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" min="0.01" step="0.01" max="{{ $stats['available_amount'] }}" required class="su-input" placeholder="0.00">
                        <span style="font-size:12px;color:var(--su-ink-40)">{{ __('instructor.max_amount') }}: {{ number_format($stats['available_amount'], 2) }} {{ __('public.currency_egp') }}</span>
                        @error('amount')<p class="su-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="su-field">
                        <label for="payment_method">{{ __('instructor.payment_receive_method') }} <span style="color:#b91c1c">*</span></label>
                        <select name="payment_method" id="payment_method" required class="su-select">
                            <option value="">{{ __('instructor.choose_payment_method') }}</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>{{ __('instructor.bank_transfer') }}</option>
                            <option value="wallet" {{ old('payment_method') == 'wallet' ? 'selected' : '' }}>{{ __('instructor.wallet') }}</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>{{ __('instructor.cash') }}</option>
                            <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>{{ __('instructor.other') }}</option>
                        </select>
                        @error('payment_method')<p class="su-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div id="bank_fields" class="su-form-grid" style="grid-template-columns:1fr 1fr;{{ old('payment_method') != 'bank_transfer' ? 'display:none' : '' }}">
                        <div class="su-field">
                            <label for="bank_name">{{ __('instructor.bank_name') }}</label>
                            <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}" class="su-input"
                                   placeholder="{{ __('instructor.placeholder_bank_example') }}">
                            @error('bank_name')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field">
                            <label for="account_holder_name">{{ __('instructor.account_holder_name') }}</label>
                            <input type="text" name="account_holder_name" id="account_holder_name" value="{{ old('account_holder_name') }}" class="su-input">
                            @error('account_holder_name')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field">
                            <label for="account_number">{{ __('instructor.account_number') }}</label>
                            <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}" class="su-input">
                            @error('account_number')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field">
                            <label for="iban">{{ __('instructor.iban') }} ({{ __('instructor.optional_label') }})</label>
                            <input type="text" name="iban" id="iban" value="{{ old('iban') }}" class="su-input" placeholder="EG...">
                            @error('iban')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="su-field">
                        <label for="notes">{{ __('instructor.notes') }} ({{ __('instructor.optional_label') }})</label>
                        <textarea name="notes" id="notes" rows="3" class="su-input" style="min-height:80px;resize:vertical"
                                  placeholder="{{ __('instructor.placeholder_extra_transfer') }}">{{ old('notes') }}</textarea>
                        @error('notes')<p class="su-field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="su-form-actions" style="margin-top:16px;padding-top:16px;border-top:1px solid var(--su-line,rgba(0,0,0,.06));justify-content:flex-end;gap:8px">
                    <a href="{{ route('instructor.withdrawals.index') }}" class="su-btn">{{ __('common.cancel') }}</a>
                    <button type="submit" class="su-btn su-btn--primary">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        {{ __('instructor.submit_request_btn') }}
                    </button>
                </div>
            </form>
        @endif
    </section>
</div>

<script>
document.getElementById('payment_method')?.addEventListener('change', function() {
    var bankFields = document.getElementById('bank_fields');
    if (bankFields) bankFields.style.display = this.value === 'bank_transfer' ? '' : 'none';
});
</script>
@endsection
