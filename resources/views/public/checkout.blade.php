@extends('layouts.lasles-public')

@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $itemTitle = $course->title ?? __('public.course_title_fallback');
    $thumbUrl = null;
    if (isset($course) && ($course->thumbnail ?? null)) {
        $thumbUrl = storage_asset(str_replace('\\', '/', $course->thumbnail));
    }
    $isMonthlyCheckout = $course->isMonthlyBilling();
    $baseCoursePrice = (float) $course->effectiveCheckoutPrice();
    $studentBal = isset($studentWalletBalance) ? (float) $studentWalletBalance : 0;
    $checkoutHasWalletBalance = isset($studentWalletBalance) && (float) $studentWalletBalance > 0;
    $fawaterakActive = ! empty($fawaterakUseGateway);
    $fawaterakMis = ! empty($fawaterakMisconfigured);
    $fawaterakIntegration = $fawaterakIntegration ?? 'iframe';
    $paypalActive = ! empty($paypalUseGateway);
    $paypalMis = ! empty($paypalMisconfigured);
    $kashierActive = ! empty($kashierUseGateway);
    $kashierMis = ! empty($kashierMisconfigured);
    $anyOnlineGateway = $fawaterakActive || $paypalActive || $kashierActive;
    $pageTitle = __('public.checkout_page_label').' — '.$itemTitle;
    $pageDescription = __('landing.checkout.hero_lead');
    $bodyClass = 'lasles-checkout-page';
    $laslesNavActive = 'courses';
@endphp

@push('head')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>[x-cloak]{display:none!important}</style>
@endpush

@section('content')
<section class="lasles-checkout-hero" aria-labelledby="checkout-title">
  <div class="lasles-container lasles-checkout-hero__inner">
    <p class="lasles-checkout-hero__crumb">
      <a href="{{ route('home') }}">{{ __('site.nav_short.home') }}</a>
      <span aria-hidden="true">/</span>
      <a href="{{ route('public.courses') }}">{{ __('landing.course_detail.crumb_courses') }}</a>
      <span aria-hidden="true">/</span>
      <a href="{{ route('public.course.show', $course->id) }}">{{ \Illuminate\Support\Str::limit($itemTitle, 36) }}</a>
      <span aria-hidden="true">/</span>
      <span>{{ __('public.checkout_breadcrumb_current') }}</span>
    </p>
    <p class="lasles-checkout-hero__kicker">{{ __('landing.checkout.kicker') }}</p>
    <h1 id="checkout-title" class="lasles-checkout-hero__title">{{ __('public.checkout_page_label') }}</h1>
    <p class="lasles-checkout-hero__lead">{{ __('landing.checkout.hero_lead') }}</p>
    <ol class="lasles-checkout-steps" aria-label="{{ __('public.checkout_steps_label') }}">
      <li class="is-done"><span>01</span>{{ __('landing.checkout.step_review') }}</li>
      <li class="is-on"><span>02</span>{{ __('landing.checkout.step_pay') }}</li>
      <li><span>03</span>{{ __('landing.checkout.step_access') }}</li>
    </ol>
  </div>
</section>

<section class="lasles-checkout-body" x-data="{ isSubmitting: false }">
  <div class="lasles-container lasles-checkout-body__grid">
    <div class="lasles-checkout-main">
      <div class="lasles-checkout-card">
        <h2>{{ __('public.checkout_payment_section_title') }}</h2>
        <p class="lasles-checkout-card__sub">{{ __('public.checkout_payment_section_desc') }}</p>

        @if(session('error'))
          <div class="lasles-checkout-alert lasles-checkout-alert--err"><p>{{ session('error') }}</p></div>
        @endif
        @if(isset($errors) && $errors->any())
          <div class="lasles-checkout-alert lasles-checkout-alert--err">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
          </div>
        @endif
        @if(session('success'))
          <div class="lasles-checkout-alert lasles-checkout-alert--ok"><p>{{ session('success') }}</p></div>
        @endif
        @if(session('info'))
          <div class="lasles-checkout-alert lasles-checkout-alert--info"><p>{{ session('info') }}</p></div>
        @endif

        <div class="lasles-checkout-panel" id="checkout-discount-panel"
             data-quote-url="{{ route('public.course.checkout.quote', $course->id) }}"
             data-has-wallet="{{ $checkoutHasWalletBalance ? '1' : '0' }}">
          @if($isMonthlyCheckout)
            <label class="lasles-checkout-check">
              <input type="checkbox" name="auto_renew" value="1" form="manual-checkout-form" {{ old('auto_renew', '1') ? 'checked' : '' }}>
              <span>
                <strong>{{ __('public.checkout_auto_renew_label') }}</strong>
                <em>{{ __('public.checkout_auto_renew_hint') }}</em>
              </span>
            </label>
          @endif

          <input type="hidden" id="checkout_currency" value="{{ platform_currency() }}">
          {{-- Synced from modal input; used by quote JS + payment forms --}}
          <input type="hidden" id="checkout_coupon_code" value="{{ old('coupon_code', '') }}">

          <div class="lasles-checkout-coupon-ask" id="checkout-coupon-ask">
            <p class="lasles-checkout-coupon-ask__q">{{ __('landing.checkout.coupon_ask') }}</p>
            <div class="lasles-checkout-coupon-ask__actions">
              <button type="button" class="lasles-btn-primary" id="checkout-coupon-yes">{{ __('landing.checkout.coupon_yes') }}</button>
              <button type="button" class="lasles-btn-outline" id="checkout-coupon-no">{{ __('landing.checkout.coupon_no') }}</button>
            </div>
          </div>

          <div class="lasles-checkout-coupon-applied hidden" id="checkout-coupon-applied">
            <div>
              <span class="lasles-checkout-coupon-applied__label">{{ __('landing.checkout.coupon_applied') }}</span>
              <strong id="checkout-coupon-applied-code"></strong>
            </div>
            <div class="lasles-checkout-coupon-applied__actions">
              <button type="button" class="lasles-checkout-linkbtn" id="checkout-coupon-change">{{ __('landing.checkout.coupon_change') }}</button>
              <button type="button" class="lasles-checkout-linkbtn is-muted" id="checkout-coupon-remove">{{ __('landing.checkout.coupon_remove') }}</button>
            </div>
          </div>

          <div class="lasles-checkout-coupon-declined hidden" id="checkout-coupon-declined">
            <p>{{ __('landing.checkout.coupon_declined_note') }}</p>
            <button type="button" class="lasles-checkout-linkbtn" id="checkout-coupon-open-later">{{ __('landing.checkout.coupon_add_later') }}</button>
          </div>

          @if($checkoutHasWalletBalance)
            <div class="lasles-checkout-wallet-block">
              <p class="lasles-checkout-balance">{{ __('landing.checkout.balance_label') }}: {{ number_format($studentWalletBalance, 2) }} {{ __('public.currency_egp') }}</p>
              <div class="lasles-checkout-field">
                <label for="checkout_wallet_credit">{{ __('landing.checkout.wallet_label') }}</label>
                <input type="number" id="checkout_wallet_credit" step="0.01" min="0" value="0" max="{{ max(0, $studentWalletBalance ?? 0) }}" class="lasles-checkout-input">
              </div>
              <button type="button" id="checkout_apply_wallet" class="lasles-btn-outline">{{ __('landing.checkout.update_price') }}</button>
            </div>
          @endif

          <span id="checkout_pricing_msg" class="lasles-checkout-msg hidden"></span>
        </div>

        <div class="lasles-checkout-modal hidden" id="checkout-coupon-modal" role="dialog" aria-modal="true" aria-labelledby="checkout-coupon-modal-title" hidden>
          <button type="button" class="lasles-checkout-modal__backdrop" id="checkout-coupon-modal-backdrop" aria-label="{{ __('landing.checkout.cancel') }}"></button>
          <div class="lasles-checkout-modal__panel">
            <h3 id="checkout-coupon-modal-title">{{ __('landing.checkout.coupon_modal_title') }}</h3>
            <p class="lasles-checkout-modal__lead">{{ __('landing.checkout.coupon_modal_lead') }}</p>
            <div class="lasles-checkout-field">
              <label for="checkout_coupon_code_input">{{ __('landing.checkout.coupon_label') }}</label>
              <input type="text" id="checkout_coupon_code_input" dir="ltr" autocomplete="off" class="lasles-checkout-input" placeholder="SAVE10">
            </div>
            <p id="checkout-coupon-modal-error" class="lasles-checkout-modal__error hidden"></p>
            <div class="lasles-checkout-modal__actions">
              <button type="button" class="lasles-btn-primary" id="checkout_apply_pricing">{{ __('landing.checkout.coupon_apply') }}</button>
              <button type="button" class="lasles-btn-outline" id="checkout-coupon-modal-close">{{ __('landing.checkout.cancel') }}</button>
            </div>
          </div>
        </div>

        @if($fawaterakMis && ! $paypalActive && ! $kashierActive)
          <div class="lasles-checkout-alert lasles-checkout-alert--err">
            <div>
              <strong>{{ __('landing.checkout.gateway_incomplete') }}</strong>
              <p>{{ __('landing.checkout.fawaterak_incomplete') }}</p>
            </div>
          </div>
          <a href="{{ route('public.course.show', $course->id) }}" class="lasles-btn-outline">{{ __('landing.checkout.back') }}</a>
        @elseif($paypalMis && ! $fawaterakActive && ! $kashierActive)
          <div class="lasles-checkout-alert lasles-checkout-alert--err">
            <div>
              <strong>{{ __('landing.checkout.paypal_incomplete_title') }}</strong>
              <p>{{ __('landing.checkout.paypal_incomplete') }}</p>
            </div>
          </div>
        @elseif($fawaterakActive && $fawaterakIntegration === 'api')
          <div class="lasles-checkout-alert lasles-checkout-alert--sky">
            <div>
              <strong>{{ __('landing.checkout.online_title') }}</strong>
              <p>{{ __('landing.checkout.online_hint') }}</p>
            </div>
          </div>
          <div id="fawaterk-api-error" class="hidden lasles-checkout-alert lasles-checkout-alert--err"></div>
          <div id="fawaterk-api-loading" class="lasles-checkout-loading">{{ __('landing.checkout.loading_methods') }}</div>
          <div id="fawaterk-api-methods" class="hidden lasles-checkout-methods"></div>
          <div id="fawaterk-api-wallet-wrap" class="hidden lasles-checkout-field">
            <label for="fawaterk-api-wallet">{{ __('landing.checkout.wallet_number') }}</label>
            <input type="text" id="fawaterk-api-wallet" dir="ltr" class="lasles-checkout-input" placeholder="01xxxxxxxxx" autocomplete="tel">
          </div>
          <div id="fawaterk-api-result" class="hidden lasles-checkout-panel"></div>
          <button type="button" id="fawaterk-api-pay-btn" disabled class="lasles-btn-primary lasles-checkout-submit">{{ __('landing.checkout.continue_pay') }}</button>
        @elseif($fawaterakActive)
          <div class="lasles-checkout-alert lasles-checkout-alert--sky">
            <div>
              <strong>{{ __('landing.checkout.fawaterak_title') }}</strong>
              <p>{{ __('landing.checkout.fawaterak_hint') }}</p>
            </div>
          </div>
          <div id="fawaterk-checkout-error" class="hidden lasles-checkout-alert lasles-checkout-alert--err"></div>
          <div id="fawaterkDivId" class="lasles-checkout-iframe"></div>
          <a href="{{ route('public.course.show', $course->id) }}" class="lasles-btn-outline">{{ __('landing.checkout.back') }}</a>
        @endif

        @if($kashierMis && ! $anyOnlineGateway && ! $fawaterakMis && ! $paypalMis)
          <div class="lasles-checkout-alert lasles-checkout-alert--err">
            <div>
              <strong>{{ __('landing.checkout.kashier_incomplete_title') }}</strong>
              <p>{{ __('landing.checkout.kashier_incomplete') }}</p>
            </div>
          </div>
        @endif

        @if($paypalActive)
          @if($fawaterakActive)
            <p class="lasles-checkout-or">{{ __('landing.checkout.or') }}</p>
          @endif
          <form method="POST" action="{{ route('public.course.checkout.paypal', $course->id) }}" id="paypal-checkout-form">
            @csrf
            <input type="hidden" name="coupon_code" id="paypal_coupon_code" value="">
            <input type="hidden" name="wallet_credit" id="paypal_wallet_credit" value="0">
            <input type="hidden" name="currency" id="paypal_currency" value="{{ platform_currency() }}">
            <button type="submit" class="lasles-btn-primary lasles-checkout-submit lasles-checkout-submit--paypal">{{ __('landing.checkout.pay_paypal') }}</button>
          </form>
        @endif

        @if($kashierActive)
          @if($fawaterakActive || $paypalActive)
            <p class="lasles-checkout-or">{{ __('landing.checkout.or') }}</p>
          @endif
          <form method="POST" action="{{ route('public.course.checkout.kashier', $course->id) }}" id="kashier-checkout-form">
            @csrf
            <input type="hidden" name="coupon_code" id="kashier_coupon_code" value="">
            <input type="hidden" name="wallet_credit" id="kashier_wallet_credit" value="0">
            <input type="hidden" name="currency" id="kashier_currency" value="{{ platform_currency() }}">
            <button type="submit" class="lasles-btn-primary lasles-checkout-submit lasles-checkout-submit--kashier">{{ __('landing.checkout.pay_kashier') }}</button>
          </form>
        @endif

        @if(! $anyOnlineGateway && ! $fawaterakMis && ! $paypalMis && ! $kashierMis)
          <div class="lasles-checkout-alert lasles-checkout-alert--info">
            <div>
              <strong>{{ __('landing.checkout.manual_title') }}</strong>
              <p>{{ __('landing.checkout.manual_hint') }}</p>
            </div>
          </div>
          <form action="{{ route('public.course.checkout.complete', $course->id) }}" method="POST" enctype="multipart/form-data" @submit="isSubmitting = true" x-data="{paymentMethod:'bank_transfer'}" id="manual-checkout-form" class="lasles-checkout-form">
            @csrf
            <input type="hidden" name="coupon_code" id="form_coupon_code" value="{{ old('coupon_code', '') }}">
            <input type="hidden" name="wallet_credit" id="form_wallet_credit" value="{{ old('wallet_credit', '0') }}">
            <input type="hidden" name="currency" id="form_currency" value="{{ old('currency', platform_currency()) }}">

            <div class="lasles-checkout-field">
              <label for="payment_method">{{ __('landing.checkout.method_label') }}</label>
              <select name="payment_method" id="payment_method" x-model="paymentMethod" class="lasles-checkout-input" required>
                <option value="bank_transfer">{{ __('landing.checkout.method_bank') }}</option>
                <option value="cash">{{ __('landing.checkout.method_cash') }}</option>
                <option value="other">{{ __('landing.checkout.method_other') }}</option>
              </select>
            </div>

            <div class="lasles-checkout-field" x-show="paymentMethod === 'bank_transfer'" x-cloak>
              <label for="wallet_id">{{ __('landing.checkout.account_label') }}</label>
              <select name="wallet_id" id="wallet_id" class="lasles-checkout-input" :required="paymentMethod === 'bank_transfer'">
                <option value="">{{ __('landing.checkout.account_placeholder') }}</option>
                @foreach(($wallets ?? []) as $wallet)
                  <option value="{{ $wallet->id }}">{{ $wallet->name ?? __('landing.checkout.platform_account') }} — {{ $wallet->account_number ?? $wallet->phone ?? '—' }}</option>
                @endforeach
              </select>
            </div>

            <div class="lasles-checkout-field">
              <label for="payment_proof">{{ __('landing.checkout.proof_label') }}</label>
              <input type="file" name="payment_proof" id="payment_proof" accept="image/*" required class="lasles-checkout-input lasles-checkout-input--file">
            </div>

            <div class="lasles-checkout-field">
              <label for="notes">{{ __('landing.checkout.notes_label') }}</label>
              <textarea name="notes" id="notes" rows="3" class="lasles-checkout-input" placeholder="{{ __('landing.checkout.notes_placeholder') }}"></textarea>
            </div>

            <div class="lasles-checkout-form__actions">
              <button type="submit" :disabled="isSubmitting" class="lasles-btn-primary lasles-checkout-submit">
                <span x-text="isSubmitting ? '{{ __('landing.checkout.submitting') }}' : '{{ __('landing.checkout.submit_order') }}'"></span>
              </button>
              <a href="{{ route('public.course.show', $course->id) }}" class="lasles-btn-outline">{{ __('landing.checkout.cancel') }}</a>
            </div>
          </form>
        @endif
      </div>
    </div>

    <aside class="lasles-checkout-side">
      <div class="lasles-checkout-side__card">
        <div class="lasles-checkout-item">
          @if($thumbUrl)
            <img src="{{ $thumbUrl }}" alt="" width="72" height="72">
          @else
            <div class="lasles-checkout-item__ph" aria-hidden="true">✦</div>
          @endif
          <div>
            <h2>{{ $course->title }}</h2>
            <p>
              {{ $course->instructor->name ?? '' }}
              @if($course->academicSubject)
                · {{ $course->academicSubject->name }}
              @endif
            </p>
          </div>
        </div>

        <h3>{{ __('public.checkout_order_summary_title') }}</h3>

        <div id="checkout-pricing-summary"
             data-base-price="{{ $baseCoursePrice }}"
             data-student-balance="{{ $studentBal }}"
             data-has-course="1"
             data-is-monthly="{{ $isMonthlyCheckout ? '1' : '0' }}">
          @if($isMonthlyCheckout)
            <p class="lasles-checkout-alert lasles-checkout-alert--sky lasles-checkout-alert--compact">{{ __('public.checkout_monthly_notice') }}</p>
          @endif
          <div class="lasles-checkout-sum-row">
            <span>{{ $isMonthlyCheckout ? __('public.checkout_monthly_price_label') : __('public.checkout_base_price_label') }}</span>
            <strong id="sum-original">{{ number_format($baseCoursePrice, 2) }} <small>{{ __('public.currency_egp') }}@if($isMonthlyCheckout)/{{ __('public.per_month') }}@endif</small></strong>
          </div>
          <div class="lasles-checkout-sum-row is-green hidden" id="sum-coupon-row">
            <span>{{ __('landing.checkout.coupon_discount') }}</span>
            <span id="sum-coupon">—</span>
          </div>
          <div class="lasles-checkout-sum-row is-blue hidden" id="sum-wallet-row">
            <span>{{ __('landing.checkout.wallet_row') }}</span>
            <span id="sum-wallet">—</span>
          </div>
          <div class="lasles-checkout-sum-total">
            <span>{{ __('landing.checkout.due_now') }}</span>
            <span id="sum-final">{{ number_format($baseCoursePrice, 2) }} <small>{{ __('public.currency_egp') }}</small></span>
          </div>
        </div>

        <ul class="lasles-checkout-benefits">
          @if($isMonthlyCheckout)
            <li>{{ __('public.checkout_benefit_monthly_access') }}</li>
          @else
            <li>{{ __('public.checkout_benefit_lifetime') }}</li>
          @endif
          <li>{{ __('public.checkout_benefit_support') }}</li>
          <li>{{ __('public.checkout_benefit_certificate') }}</li>
        </ul>

        <div class="lasles-checkout-trust">
          <span>{{ __('landing.checkout.trust_secure') }}</span>
          <span>{{ __('landing.checkout.trust_fast') }}</span>
          <span>{{ __('landing.checkout.trust_support') }}</span>
        </div>
      </div>
    </aside>
  </div>
</section>
@endsection

@push('scripts')
@include('public.partials.checkout-scripts')
@if(!empty($paypalUseGateway))
<script>
(function(){
  var form = document.getElementById('paypal-checkout-form');
  if (!form) return;
  form.addEventListener('submit', function(){
    var c = document.getElementById('checkout_coupon_code');
    var w = document.getElementById('checkout_wallet_credit');
    var pc = document.getElementById('paypal_coupon_code');
    var pw = document.getElementById('paypal_wallet_credit');
    var pcur = document.getElementById('paypal_currency');
    if (pc) pc.value = c ? (c.value || '').trim() : '';
    if (pw) pw.value = w && w.value !== '' ? w.value : '0';
    if (pcur) pcur.value = @json(platform_currency());
  });
})();
</script>
@endif
@if(!empty($kashierUseGateway))
<script>
(function(){
  var form = document.getElementById('kashier-checkout-form');
  if (!form) return;
  form.addEventListener('submit', function(){
    var c = document.getElementById('checkout_coupon_code');
    var w = document.getElementById('checkout_wallet_credit');
    var kc = document.getElementById('kashier_coupon_code');
    var kw = document.getElementById('kashier_wallet_credit');
    if (kc) kc.value = c ? (c.value || '').trim() : '';
    if (kw) kw.value = w && w.value !== '' ? w.value : '0';
  });
})();
</script>
@endif
@endpush
