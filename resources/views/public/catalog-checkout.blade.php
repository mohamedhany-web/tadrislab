@php
  /** @var string $kind package|learning_path */
  $isPackage = ($kind ?? '') === 'package';
  $isRtl = app()->getLocale() === 'ar';
  $pageTitle = $pageTitle ?? (__('public.checkout_page_label').' — '.$title);
  $pageDescription = $isPackage
      ? __('landing.checkout.catalog_hero_lead_package')
      : __('landing.checkout.catalog_hero_lead_path');
  $bodyClass = 'lasles-checkout-page';
  $laslesNavActive = $laslesNavActive ?? ($isPackage ? 'pricing' : 'teacher-paths');
  $subtitle = $subtitle ?? null;
  $durationLabel = $durationLabel ?? null;
  $benefits = array_values(array_filter($benefits ?? []));
  $defaultMethod = old('payment_method');
  if (! $defaultMethod) {
      if (! empty($kashierReady)) {
          $defaultMethod = 'kashier';
      } elseif (! empty($paypalReady)) {
          $defaultMethod = 'paypal';
      } else {
          $defaultMethod = 'bank_transfer';
      }
  }
  $pricingCrumb = $isPackage
      ? route('public.pricing')
      : route('public.learning-paths.index');
  $pricingCrumbLabel = $isPackage
      ? __('public.pricing_page_title')
      : __('landing.checkout.catalog_crumb_paths');
@endphp
@extends('layouts.lasles-public')

@section('content')
<section class="lasles-checkout-hero" aria-labelledby="checkout-title">
  <div class="lasles-container lasles-checkout-hero__inner">
    <p class="lasles-checkout-hero__crumb">
      <a href="{{ route('home') }}">{{ __('site.nav_short.home') }}</a>
      <span aria-hidden="true">/</span>
      <a href="{{ $pricingCrumb }}">{{ $pricingCrumbLabel }}</a>
      <span aria-hidden="true">/</span>
      <a href="{{ $backUrl }}">{{ \Illuminate\Support\Str::limit($title, 36) }}</a>
      <span aria-hidden="true">/</span>
      <span>{{ __('public.checkout_breadcrumb_current') }}</span>
    </p>
    <p class="lasles-checkout-hero__kicker">{{ __('landing.checkout.kicker') }}</p>
    <h1 id="checkout-title" class="lasles-checkout-hero__title">{{ __('public.checkout_page_label') }}</h1>
    <p class="lasles-checkout-hero__lead">
      {{ $isPackage ? __('landing.checkout.catalog_hero_lead_package') : __('landing.checkout.catalog_hero_lead_path') }}
    </p>
    <ol class="lasles-checkout-steps" aria-label="{{ __('public.checkout_steps_label') }}">
      <li class="is-done">
        <span>01</span>
        {{ $isPackage ? __('landing.checkout.catalog_step_review_package') : __('landing.checkout.catalog_step_review_path') }}
      </li>
      <li class="is-on"><span>02</span>{{ __('landing.checkout.step_pay') }}</li>
      <li><span>03</span>{{ __('landing.checkout.step_access') }}</li>
    </ol>
  </div>
</section>

<section class="lasles-checkout-body">
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

        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="lasles-checkout-form" id="catalog-checkout-form">
          @csrf

          @if($isFree)
            <div class="lasles-checkout-alert lasles-checkout-alert--sky">
              <div>
                <strong>{{ __('landing.checkout.catalog_free_title') }}</strong>
                <p>{{ __('landing.checkout.catalog_free_hint') }}</p>
              </div>
            </div>
            <input type="hidden" name="payment_method" value="online">
            <div class="lasles-checkout-form__actions">
              <button type="submit" class="lasles-btn-primary lasles-checkout-submit">{{ __('landing.checkout.catalog_activate_free') }}</button>
              <a href="{{ $backUrl }}" class="lasles-btn-outline">{{ __('landing.checkout.cancel') }}</a>
            </div>
          @else
            <fieldset class="lasles-checkout-payset">
              <legend>{{ __('landing.checkout.method_label') }}</legend>
              <div class="lasles-checkout-payopts" role="radiogroup" aria-label="{{ __('landing.checkout.method_label') }}">
                @if(! empty($kashierReady))
                  <label class="lasles-checkout-payopt">
                    <input type="radio" name="payment_method" value="kashier" @checked($defaultMethod === 'kashier')>
                    <span class="lasles-checkout-payopt__body">
                      <strong>{{ __('landing.checkout.pay_kashier') }}</strong>
                      <em>{{ __('landing.checkout.catalog_method_kashier_hint') }}</em>
                    </span>
                  </label>
                @endif
                @if(! empty($paypalReady))
                  <label class="lasles-checkout-payopt">
                    <input type="radio" name="payment_method" value="paypal" @checked($defaultMethod === 'paypal')>
                    <span class="lasles-checkout-payopt__body">
                      <strong>{{ __('landing.checkout.pay_paypal') }}</strong>
                      <em>{{ __('landing.checkout.catalog_method_paypal_hint') }}</em>
                    </span>
                  </label>
                @endif
                <label class="lasles-checkout-payopt">
                  <input type="radio" name="payment_method" value="bank_transfer" @checked($defaultMethod === 'bank_transfer')>
                  <span class="lasles-checkout-payopt__body">
                    <strong>{{ __('landing.checkout.catalog_method_bank') }}</strong>
                    <em>{{ __('landing.checkout.catalog_method_bank_hint') }}</em>
                  </span>
                </label>
              </div>
            </fieldset>

            <div class="lasles-checkout-field" data-bank-proof>
              <label for="payment_proof">{{ __('landing.checkout.catalog_proof_label') }}</label>
              <input id="payment_proof" type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" class="lasles-checkout-input lasles-checkout-input--file">
              <p class="lasles-checkout-field__hint">{{ __('landing.checkout.catalog_proof_hint') }}</p>
            </div>

            <div class="lasles-checkout-form__actions">
              <button type="submit" class="lasles-btn-primary lasles-checkout-submit">{{ __('landing.checkout.continue_pay') }}</button>
              <a href="{{ $backUrl }}" class="lasles-btn-outline">{{ __('landing.checkout.cancel') }}</a>
            </div>
          @endif
        </form>
      </div>
    </div>

    <aside class="lasles-checkout-side">
      <div class="lasles-checkout-side__card">
        <div class="lasles-checkout-item">
          <div class="lasles-checkout-item__ph" aria-hidden="true">{{ $isPackage ? '◆' : '◈' }}</div>
          <div>
            <h2>{{ $title }}</h2>
            @if($subtitle)
              <p>{{ $subtitle }}</p>
            @elseif($durationLabel)
              <p>{{ $durationLabel }}</p>
            @else
              <p>{{ $isPackage ? __('landing.checkout.catalog_item_package') : __('landing.checkout.catalog_item_path') }}</p>
            @endif
          </div>
        </div>

        <h3>{{ __('public.checkout_order_summary_title') }}</h3>

        <div class="lasles-checkout-sum-row">
          <span>{{ __('landing.checkout.catalog_amount_label') }}</span>
          <strong>{{ $priceLabel }}</strong>
        </div>
        @if($durationLabel)
          <div class="lasles-checkout-sum-row">
            <span>{{ __('landing.checkout.catalog_duration_label') }}</span>
            <strong>{{ $durationLabel }}</strong>
          </div>
        @endif
        <div class="lasles-checkout-sum-total">
          <span>{{ __('landing.checkout.due_now') }}</span>
          <span>{{ $priceLabel }}</span>
        </div>

        @if(count($benefits))
          <ul class="lasles-checkout-benefits">
            @foreach(array_slice($benefits, 0, 5) as $benefit)
              <li>{{ $benefit }}</li>
            @endforeach
          </ul>
        @else
          <ul class="lasles-checkout-benefits">
            <li>{{ __('landing.checkout.catalog_benefit_access') }}</li>
            <li>{{ __('landing.checkout.catalog_benefit_progress') }}</li>
            <li>{{ __('public.checkout_benefit_support') }}</li>
          </ul>
        @endif

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
@unless($isFree)
<script>
(function () {
  var form = document.getElementById('catalog-checkout-form');
  if (!form) return;
  var proof = form.querySelector('[data-bank-proof]');
  var inputs = form.querySelectorAll('input[name="payment_method"]');
  function sync() {
    var checked = form.querySelector('input[name="payment_method"]:checked');
    var isBank = checked && checked.value === 'bank_transfer';
    if (proof) proof.hidden = !isBank;
  }
  inputs.forEach(function (el) { el.addEventListener('change', sync); });
  sync();
})();
</script>
@endunless
@endpush
