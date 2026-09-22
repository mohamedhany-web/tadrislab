@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'TADRIS LAB');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $isRtl ? 'إتمام الاشتراك' : 'Checkout' }} — {{ $brand }}</title>
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme']])
  <style>
    .gl-tc { padding: 32px 0 64px; }
    .gl-tc-card { background:#fff; border:1.5px solid #D7DDE6; border-radius:18px; padding:1.25rem; box-shadow:0 12px 28px -20px rgba(11,61,145,.3); max-width:560px; margin:0 auto; }
    .gl-tc h1 { margin:0 0 .5rem; font-size:1.35rem; font-weight:900; color:#0B1220; }
    .gl-tc-sum { background:#F4F7FC; border-radius:12px; padding:1rem; margin:1rem 0; }
    .gl-tc-sum strong { color:#0B3D91; font-size:1.25rem; }
    .gl-tc label { display:block; font-size:.78rem; font-weight:700; color:#5B6577; margin:.75rem 0 .35rem; }
    .gl-tc select, .gl-tc input { width:100%; border:1.5px solid #D7DDE6; border-radius:12px; padding:.7rem .85rem; }
    .gl-tc-err { background:#FEF2F2; color:#991B1B; padding:.75rem; border-radius:12px; margin-bottom:1rem; font-size:.86rem; }
    .gl-tc-hint { font-size:.75rem; color:#5B6577; margin:.35rem 0 0; }
  </style>
</head>
<body class="sana-home sana-courses-page">
@include('partials.landing.navbar', ['navActive' => 'groups', 'navSolid' => true, 'navHero' => false])
<main class="sana-container gl-tc">
  <div class="gl-tc-card sana-reveal">
    <h1>{{ $isRtl ? 'إتمام الاشتراك' : 'Complete subscription' }}</h1>
    <p style="color:#5B6577;font-size:.9rem;margin:0">{{ $group->title }}</p>

    @if($errors->any())
      <div class="gl-tc-err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <div class="gl-tc-sum">
      @if($package)
        <div style="font-weight:800">{{ $package->name }}</div>
        <div style="font-size:.85rem;color:#5B6577;margin:.35rem 0">{{ $package->sessions_count }} {{ $isRtl ? 'حصة' : 'sessions' }} · {{ $package->duration_months }} {{ $isRtl ? 'شهر' : 'mo' }}</div>
        @if($package->formattedOriginalPrice())
          <div style="text-decoration:line-through;color:#94A3B8;font-size:.85rem">{{ $package->formattedOriginalPrice() }}</div>
        @endif
        <strong>{{ $package->formattedPrice() }}</strong>
      @elseif($cohort)
        <div style="font-weight:800">{{ $cohort->title }}</div>
        <div style="font-size:.85rem;color:#5B6577;margin:.35rem 0">{{ $cohort->statusLabel() }} · {{ $cohort->seatsLeft() }} {{ $isRtl ? 'مقعد متبقي' : 'seats left' }}</div>
        <strong>{{ $group->formattedPrice() }}</strong>
      @endif
    </div>

    <form method="POST" action="{{ route('public.groups.checkout.store', $group->slug) }}" enctype="multipart/form-data" id="tutoring-checkout-form">
      @csrf
      @if($package)<input type="hidden" name="package_id" value="{{ $package->id }}">@endif
      @if($cohort)<input type="hidden" name="cohort_id" value="{{ $cohort->id }}">@endif
      @if($startsAt)<input type="hidden" name="starts_at" value="{{ $startsAt }}">@endif

      <label for="payment_method">{{ $isRtl ? 'طريقة الدفع' : 'Payment method' }}</label>
      <select id="payment_method" name="payment_method" required>
        <option value="online">{{ $isRtl ? 'دفع أونلاين' : 'Online payment' }}</option>
        <option value="bank_transfer" selected>{{ $isRtl ? 'تحويل على حساب المنصة' : 'Transfer to platform account' }}</option>
      </select>

      <div data-manual-fields>
        <label for="wallet_id">{{ $isRtl ? 'الحساب المحوَّل إليه' : 'Receiving account' }}</label>
        <select id="wallet_id" name="wallet_id">
          <option value="">{{ $isRtl ? 'اختر الحساب' : 'Select account' }}</option>
          @forelse(($wallets ?? collect()) as $w)
            <option value="{{ $w->id }}" @selected((string) old('wallet_id') === (string) $w->id)>{{ $w->checkoutLabel() }}</option>
          @empty
            <option value="" disabled>{{ $isRtl ? 'لا توجد حسابات نشطة' : 'No active accounts' }}</option>
          @endforelse
        </select>
        <p class="gl-tc-hint">{{ $isRtl ? 'حوّل على أحد حسابات المنصة ثم ارفع إثبات التحويل.' : 'Transfer to a platform account, then upload proof.' }}</p>

        <label for="payment_proof">{{ $isRtl ? 'إثبات التحويل' : 'Transfer proof' }}</label>
        <input id="payment_proof" type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf,image/*">
      </div>

      <button type="submit" class="sana-btn sana-btn--yellow" style="width:100%;justify-content:center;margin-top:1.25rem">
        <i class="fas fa-lock"></i> {{ $isRtl ? 'تأكيد الطلب' : 'Confirm order' }}
      </button>
    </form>
    <p style="margin:1rem 0 0;font-size:.78rem;color:#5B6577;text-align:center">
      <a href="{{ route('public.groups.show', $group->slug) }}">{{ $isRtl ? 'رجوع للمجموعة' : 'Back to group' }}</a>
    </p>
  </div>
</main>
@include('partials.landing.footer')
<script>
(function () {
  var form = document.getElementById('tutoring-checkout-form');
  if (!form) return;
  var fields = form.querySelector('[data-manual-fields]');
  var wallet = form.querySelector('#wallet_id');
  var proof = form.querySelector('#payment_proof');
  var method = form.querySelector('#payment_method');
  function sync() {
    var isManual = method && method.value === 'bank_transfer';
    if (fields) fields.hidden = !isManual;
    if (wallet) wallet.required = !!isManual;
    if (proof) proof.required = !!isManual;
  }
  if (method) method.addEventListener('change', sync);
  sync();
})();
</script>
</body>
</html>
