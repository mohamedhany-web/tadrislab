@extends('layouts.instructor-timeline')

@section('title', __('instructor.transfer_account') . ' - ' . config('app.name'))
@section('page_title', __('instructor.transfer_account'))

@section('content')
<div class="su-page" style="max-width:56rem">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-university su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.transfer_account') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.transfer_account_desc') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            <i class="fas fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
        </div>
    @endif

    <section class="su-card">
        <form action="{{ route('instructor.transfer-account.store') }}" method="POST">
            @csrf
            <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
                <div class="su-field">
                    <label for="bank_name">{{ __('instructor.bank_name') }}</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $detail->bank_name) }}" class="su-input"
                           placeholder="{{ __('instructor.placeholder_bank_example') }}">
                    @error('bank_name')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field">
                    <label for="account_holder_name">{{ __('instructor.account_holder_name') }}</label>
                    <input type="text" name="account_holder_name" id="account_holder_name" value="{{ old('account_holder_name', $detail->account_holder_name) }}" class="su-input"
                           placeholder="{{ __('instructor.placeholder_name_on_card') }}">
                    @error('account_holder_name')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field">
                    <label for="account_number">{{ __('instructor.account_number') }}</label>
                    <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $detail->account_number) }}" dir="ltr" class="su-input"
                           placeholder="{{ __('instructor.placeholder_account_number') }}">
                    @error('account_number')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field">
                    <label for="iban">{{ __('instructor.iban') }}</label>
                    <input type="text" name="iban" id="iban" value="{{ old('iban', $detail->iban) }}" dir="ltr" class="su-input" placeholder="EG...">
                    @error('iban')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field">
                    <label for="branch_name">{{ __('instructor.branch_name') }}</label>
                    <input type="text" name="branch_name" id="branch_name" value="{{ old('branch_name', $detail->branch_name) }}" class="su-input"
                           placeholder="{{ __('instructor.placeholder_branch_optional') }}">
                    @error('branch_name')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field">
                    <label for="swift_code">{{ __('instructor.swift_code') }}</label>
                    <input type="text" name="swift_code" id="swift_code" value="{{ old('swift_code', $detail->swift_code) }}" dir="ltr" class="su-input"
                           placeholder="{{ __('instructor.placeholder_optional') }}">
                    @error('swift_code')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field" style="grid-column:1 / -1">
                    <label for="notes">{{ __('instructor.notes') }}</label>
                    <textarea name="notes" id="notes" rows="2" class="su-input" style="min-height:64px;resize:vertical"
                              placeholder="{{ __('instructor.placeholder_extra_transfer') }}">{{ old('notes', $detail->notes) }}</textarea>
                    @error('notes')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="su-form-actions" style="margin-top:16px">
                <button type="submit" class="su-btn su-btn--primary">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    {{ __('instructor.save_transfer_data') }}
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
