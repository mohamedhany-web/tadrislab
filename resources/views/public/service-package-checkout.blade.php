@php
  $locale = app()->getLocale();
  $isRtl = $locale === 'ar';
  $brand = config('app.name', 'TADRIS LAB');
  $footer = \App\Services\PublicFooterSettings::payload();
  $waUrl = $footer['whatsapp_url'] ?? '#';
  $perMonth = $package->sessionsPerMonth();
  $fawaterakActive = !empty($fawaterakUseGateway);
  $fawaterakMis = !empty($fawaterakMisconfigured);
  $fawaterakIntegration = $fawaterakIntegration ?? 'iframe';
  $paypalActive = !empty($paypalUseGateway);
  $paypalMis = !empty($paypalMisconfigured);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ $package->name }} — {{ $brand }}</title>
  <meta name="robots" content="noindex">
  <meta name="theme-color" content="#0B3D91">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog', 'pricing']])
  <style>
    .gl-co-alert--sky { background:#EEF4FF; color:#0B3D91; border:1px solid #C9D8F5; }
    .gl-co-alert--info { background:#F8FAFC; color:#334155; border:1px solid #E2E8F0; }
    .hidden { display:none !important; }
    /* .sana-cat-page already offsets the fixed navbar; drop the pricing-page offset. */
    body.sana-pricing-page { padding-top: 0; }
    .gl-co { padding: clamp(24px, 4vw, 40px) 0 72px; }
    .gl-co-grid { display: grid; gap: 1.25rem; }
    @media (min-width: 960px) { .gl-co-grid { grid-template-columns: 1fr .85fr; align-items: start; } }
    .gl-co-card { background: #fff; border: 1.5px solid #D7DDE6; border-radius: 20px; box-shadow: 0 14px 34px -24px rgba(11,61,145,.4); overflow: hidden; }
    .gl-co-card__head { padding: 1.15rem 1.25rem; border-bottom: 1px dashed #E4E9F2; }
    .gl-co-card__head h1, .gl-co-card__head h2 { margin: 0; font-family: Cairo, Tajawal, sans-serif; font-weight: 900; color: #0B1220; font-size: 1.2rem; }
    .gl-co-card__body { padding: 1.15rem 1.25rem 1.35rem; }

    .gl-co-specs { list-style: none; margin: 0; padding: 0; display: grid; }
    .gl-co-specs li { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .6rem 0; border-bottom: 1px solid #F1F4F9; font-size: .86rem; }
    .gl-co-specs li:last-child { border-bottom: 0; }
    .gl-co-specs__k { display: inline-flex; align-items: center; gap: .45rem; color: #5B6577; font-weight: 700; }
    .gl-co-specs__k i { color: #0B3D91; width: 1rem; text-align: center; font-size: .78rem; }
    .gl-co-specs__v { color: #0B1220; font-weight: 900; text-align: end; }
    .gl-co-specs__v small { display: block; font-size: .72rem; font-weight: 700; color: #5B6577; }

    .gl-co-total { margin-top: 1rem; padding: .9rem 1rem; border-radius: 14px; background: #F4F7FC; border: 1.5px solid #DCE5F5; display: flex; align-items: center; justify-content: space-between; gap: .75rem; }
    .gl-co-total span { font-weight: 800; color: #5B6577; font-size: .85rem; }
    .gl-co-total strong { font-family: Cairo, sans-serif; font-size: 1.6rem; font-weight: 900; color: #0B3D91; line-height: 1; }

    .gl-co-label { display: block; margin-bottom: .35rem; font-size: .76rem; font-weight: 800; color: #5B6577; }
    .gl-co-select { width: 100%; border: 1.5px solid #D7DDE6; border-radius: 12px; padding: .7rem .85rem; font-size: .9rem; background: #fff; color: #0B1220; margin-bottom: .95rem; font-weight: 700; }
    .gl-co-hint { font-size: .76rem; color: #5B6577; line-height: 1.7; margin: 0 0 1rem; }
    .gl-co-alert { padding: .75rem 1rem; border-radius: 12px; margin-bottom: 1rem; font-size: .85rem; font-weight: 700; }
    .gl-co-alert--err { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
    .gl-co-steps { list-style: none; margin: 1rem 0 0; padding: 0; display: grid; gap: .5rem; }
    .gl-co-steps li { display: flex; gap: .5rem; font-size: .8rem; color: #5B6577; line-height: 1.65; }
    .gl-co-steps i { color: #0B3D91; margin-top: .25rem; }
  </style>
</head>
<body class="sana-home sana-courses-page sana-pricing-page">
<div id="sana-scroll-progress"></div>
@include('partials.landing.navbar', ['navActive' => 'packages', 'navSolid' => true, 'navHero' => false])

<main class="sana-cat-page">
  <section class="sana-cat-hero" style="padding-bottom:.5rem">
    <div class="sana-container sana-cat-hero__inner sana-reveal">
      <nav class="sana-cat-hero__breadcrumb" aria-label="breadcrumb">
        <a href="{{ route('home') }}">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('public.service-packages.index') }}">{{ $isRtl ? 'باقات الحصص' : 'Packages' }}</a>
        <span aria-hidden="true">/</span>
        <span>{{ $package->name }}</span>
      </nav>
      <h1 class="sana-cat-hero__title" style="margin-top:.6rem">{{ $isRtl ? 'تأكيد طلب' : 'Confirm' }} <span class="hl">{{ $package->name }}</span></h1>
      <p class="sana-cat-hero__sub">{{ $isRtl ? 'راجع تفاصيل الباقة بالأرقام قبل الدفع.' : 'Review the pack details before paying.' }}</p>
    </div>
  </section>

  <div class="sana-container gl-co">
    @if(session('error'))
      <div class="gl-co-alert gl-co-alert--err">{{ session('error') }}</div>
    @endif
    @if(session('info'))
      <div class="gl-co-alert gl-co-alert--sky">{{ session('info') }}</div>
    @endif
    @if(isset($errors) && $errors->any())
      <div class="gl-co-alert gl-co-alert--err">{{ $errors->first() }}</div>
    @endif

    <div class="gl-co-grid">
      <article class="gl-co-card sana-reveal">
        <div class="gl-co-card__head">
          <h1>{{ $isRtl ? 'ماذا ستحصل عليه' : 'What you get' }}</h1>
          @if($package->isCommercialPlan())
            <p style="margin:.4rem 0 0;font-size:.82rem;color:#5B6577;font-weight:700">{{ $package->planLabel() }} · {{ $package->termLabel() }}</p>
          @endif
        </div>
        <div class="gl-co-card__body">
          @if($package->tagline || $package->description)
            <p class="gl-co-hint">{{ $package->tagline ?: $package->description }}</p>
          @endif

          <ul class="gl-co-specs">
            @if($package->isCommercialPlan())
              <li>
                <span class="gl-co-specs__k"><i class="fas fa-calendar-week"></i> {{ $isRtl ? 'حصص أسبوعياً' : 'Weekly sessions' }}</span>
                <span class="gl-co-specs__v">{{ $package->weeklySessionsTotal() }}</span>
              </li>
            @endif
            <li>
              <span class="gl-co-specs__k"><i class="fas fa-layer-group"></i> {{ $isRtl ? 'عدد الحصص' : 'Sessions' }}</span>
              <span class="gl-co-specs__v">{{ $package->units_count }} {{ $isRtl ? 'حصة' : 'sessions' }}</span>
            </li>
            <li>
              <span class="gl-co-specs__k"><i class="fas fa-hourglass-half"></i> {{ $isRtl ? 'مدة الحصة' : 'Session length' }}</span>
              <span class="gl-co-specs__v">{{ $package->sessionMinutes() }} {{ $isRtl ? 'دقيقة' : 'min' }}</span>
            </li>
            <li>
              <span class="gl-co-specs__k"><i class="fas fa-clock"></i> {{ $isRtl ? 'إجمالي وقت التعلّم' : 'Total learning time' }}</span>
              <span class="gl-co-specs__v">{{ $package->totalHoursLabel() }}</span>
            </li>
            <li>
              <span class="gl-co-specs__k"><i class="fas fa-coins"></i> {{ $isRtl ? 'سعر الحصة الواحدة' : 'Price per session' }}</span>
              <span class="gl-co-specs__v">{{ $package->formattedPricePerUnit() }}</span>
            </li>
            <li>
              <span class="gl-co-specs__k"><i class="fas fa-calendar-day"></i> {{ $isRtl ? 'صلاحية الرصيد' : 'Validity' }}</span>
              <span class="gl-co-specs__v">
                {{ $package->validityLabel() }}
                @if($perMonth)
                  <small>{{ $isRtl ? 'بمعدل' : 'about' }} {{ rtrim(rtrim(number_format($perMonth, 1), '0'), '.') }} {{ $isRtl ? 'حصة/شهر' : 'sessions/mo' }}</small>
                @endif
              </span>
            </li>
            <li>
              <span class="gl-co-specs__k"><i class="fas fa-chalkboard-user"></i> {{ $isRtl ? 'تُستخدم في' : 'Valid for' }}</span>
              <span class="gl-co-specs__v">
                {{ $package->label() }}
                <small>{{ $package->scopeUsageHint() }}</small>
              </span>
            </li>
            @if($package->savingsVsMonthlyLabel())
              <li>
                <span class="gl-co-specs__k"><i class="fas fa-piggy-bank"></i> {{ $isRtl ? 'التوفير' : 'You save' }}</span>
                <span class="gl-co-specs__v" style="color:#047857">{{ $package->savingsVsMonthlyLabel() }}</span>
              </li>
            @elseif($package->formattedOriginalPrice())
              <li>
                <span class="gl-co-specs__k"><i class="fas fa-tag"></i> {{ $isRtl ? 'السعر قبل الخصم' : 'Before discount' }}</span>
                <span class="gl-co-specs__v" style="color:#94A3B8;text-decoration:line-through">{{ $package->formattedOriginalPrice() }}</span>
              </li>
              <li>
                <span class="gl-co-specs__k"><i class="fas fa-piggy-bank"></i> {{ $isRtl ? 'التوفير' : 'You save' }}</span>
                <span class="gl-co-specs__v" style="color:#047857">
                  ${{ number_format($package->savingsAmount(), 2) }} {{ platform_currency() }} ({{ $package->savingsPercent() }}%)
                </span>
              </li>
            @endif
          </ul>

          <div class="gl-co-total">
            <span>{{ $isRtl ? 'الإجمالي المطلوب' : 'Total due' }}</span>
            <strong>{{ $package->formattedPrice() }}</strong>
          </div>

          <ul class="gl-co-steps">
            <li><i class="fas fa-circle-check"></i><span>{{ $isRtl ? 'بعد إتمام الدفع عبر فواتيرك يُضاف الرصيد لحسابك تلقائياً.' : 'Credits are added automatically after Fawaterak payment succeeds.' }}</span></li>
            <li><i class="fas fa-circle-check"></i><span>{{ $isRtl ? 'تُخصم حصة واحدة فقط عند اكتمال الدرس، وليس عند الحجز.' : 'One unit is deducted when the lesson completes, not at booking.' }}</span></li>
            <li><i class="fas fa-circle-check"></i><span>{{ $isRtl ? 'يمكنك متابعة رصيدك من صفحة «رصيد الحصص».' : 'Track your balance from the credits page.' }}</span></li>
          </ul>
        </div>
      </article>

      <article class="gl-co-card sana-reveal">
        <div class="gl-co-card__head">
          <h2>{{ $isRtl ? 'بيانات الدفع' : 'Payment details' }}</h2>
        </div>
        <div class="gl-co-card__body">
          @if($fawaterakMis && ! $paypalActive)
            <div class="gl-co-alert gl-co-alert--err" style="display:flex;gap:.65rem;align-items:flex-start">
              <i class="fas fa-exclamation-triangle" style="margin-top:.2rem"></i>
              <div>
                <strong>{{ $isRtl ? 'إعدادات الدفع غير مكتملة' : 'Payment settings incomplete' }}</strong>
                <p class="gl-co-hint" style="margin:.35rem 0 0">{{ $isRtl ? 'تم تفعيل فواتيرك لكن الربط غير مكتمل على الخادم.' : 'Fawaterak is enabled but server credentials are incomplete.' }}</p>
              </div>
            </div>
          @elseif($fawaterakActive && $fawaterakIntegration === 'api')
            <div class="gl-co-alert gl-co-alert--sky" style="display:flex;gap:.65rem;align-items:flex-start;margin-bottom:1rem">
              <i class="fas fa-lock" style="margin-top:.2rem"></i>
              <div>
                <strong>{{ $isRtl ? 'الدفع عبر فواتيرك' : 'Pay with Fawaterak' }}</strong>
                <p class="gl-co-hint" style="margin:.25rem 0 0">{{ $isRtl ? 'اختر وسيلة الدفع ثم تابع. بعد النجاح يُفعَّل رصيد الحصص تلقائياً.' : 'Choose a method and continue. Credits activate automatically after success.' }}</p>
              </div>
            </div>
            <div id="fawaterk-api-error" class="hidden gl-co-alert gl-co-alert--err"></div>
            <div id="fawaterk-api-loading" style="margin-bottom:1rem;font:700 .85rem Tajawal,sans-serif;color:#5B6577"><i class="fas fa-spinner fa-spin" style="color:#0B3D91"></i> {{ $isRtl ? 'جاري تحميل وسائل الدفع...' : 'Loading payment methods…' }}</div>
            <div id="fawaterk-api-methods" class="hidden" style="display:grid;gap:.55rem;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));margin-bottom:1rem"></div>
            <div id="fawaterk-api-wallet-wrap" class="hidden" style="margin-bottom:.95rem">
              <label class="gl-co-label" for="fawaterk-api-wallet">{{ $isRtl ? 'رقم المحفظة (إن لزم لوسيلة فواتيرك)' : 'Wallet number (if required by Fawaterak)' }}</label>
              <input type="text" id="fawaterk-api-wallet" dir="ltr" class="gl-co-select" placeholder="01xxxxxxxxx" autocomplete="tel">
            </div>
            <div id="fawaterk-api-result" class="hidden gl-co-alert gl-co-alert--info" style="font:600 .85rem Tajawal,sans-serif;margin-bottom:1rem"></div>
            <button type="button" id="fawaterk-api-pay-btn" disabled class="sana-btn sana-btn--yellow" style="width:100%;justify-content:center">
              <i class="fas fa-lock"></i> {{ $isRtl ? 'متابعة الدفع' : 'Continue payment' }} · {{ $package->formattedPrice() }}
            </button>
          @elseif($fawaterakActive)
            <div class="gl-co-alert gl-co-alert--sky" style="display:flex;gap:.65rem;align-items:flex-start;margin-bottom:1rem">
              <i class="fas fa-lock" style="margin-top:.2rem"></i>
              <div>
                <strong>{{ $isRtl ? 'الدفع عبر فواتيرك' : 'Pay with Fawaterak' }}</strong>
                <p class="gl-co-hint" style="margin:.25rem 0 0">{{ $isRtl ? 'اختر وسيلة الدفع داخل الإطار. بعد النجاح يُفعَّل رصيد الحصص تلقائياً.' : 'Choose a method below. Credits activate automatically after success.' }}</p>
              </div>
            </div>
            <div id="fawaterk-checkout-error" class="hidden gl-co-alert gl-co-alert--err"></div>
            <div id="fawaterkDivId"></div>
          @endif

          @if($paypalActive)
            @if($fawaterakActive)
              <p style="margin:1rem 0 .65rem;text-align:center;font:800 .8rem Tajawal,sans-serif;color:#5B6577">{{ $isRtl ? 'أو' : 'or' }}</p>
            @endif
            <form method="POST" action="{{ route('public.service-packages.paypal', $package) }}">
              @csrf
              <button type="submit" class="sana-btn sana-btn--yellow" style="width:100%;justify-content:center;background:#003087;color:#fff;border:0">
                <i class="fab fa-paypal"></i>
                {{ $isRtl ? 'الدفع عبر PayPal' : 'Pay with PayPal' }} · {{ $package->formattedPrice() }}
              </button>
            </form>
          @elseif(! $fawaterakActive && $paypalMis)
            <div class="gl-co-alert gl-co-alert--err" style="display:flex;gap:.65rem;align-items:flex-start">
              <i class="fas fa-exclamation-triangle" style="margin-top:.2rem"></i>
              <div>
                <strong>{{ $isRtl ? 'إعدادات PayPal غير مكتملة' : 'PayPal settings incomplete' }}</strong>
                <p class="gl-co-hint" style="margin:.35rem 0 0">{{ $isRtl ? 'تم تفعيل PayPal لكن بيانات الاتصال ناقصة في إعدادات النظام.' : 'PayPal is enabled but connection data is missing in system settings.' }}</p>
              </div>
            </div>
          @elseif(! $fawaterakActive && ! $fawaterakMis)
            <div class="gl-co-alert gl-co-alert--info" style="display:flex;gap:.65rem;align-items:flex-start">
              <i class="fas fa-circle-info" style="margin-top:.2rem"></i>
              <div>
                <strong>{{ $isRtl ? 'الدفع الإلكتروني غير متاح' : 'Online payment unavailable' }}</strong>
                <p class="gl-co-hint" style="margin:.35rem 0 0">{{ $isRtl ? 'بوابة فواتيرك غير مفعّلة حالياً. تواصل معنا عبر واتساب لإتمام الشراء.' : 'Fawaterak is not enabled right now. Contact us on WhatsApp to complete your purchase.' }}</p>
              </div>
            </div>
          @endif

          <div style="margin-top:1rem;display:flex;gap:.5rem;flex-wrap:wrap">
            <a href="{{ route('public.service-packages.index') }}" class="sana-btn sana-btn--white-outline" style="flex:1;justify-content:center;min-width:9rem">
              {{ $isRtl ? 'باقة أخرى' : 'Other packages' }}
            </a>
            <a href="{{ $waUrl }}" class="sana-btn sana-btn--wa" style="flex:1;justify-content:center;min-width:9rem" target="_blank" rel="noopener">
              <i class="fab fa-whatsapp"></i> {{ $isRtl ? 'استفسار' : 'Ask us' }}
            </a>
          </div>
        </div>
      </article>
    </div>
  </div>
</main>

@include('partials.landing.footer')
@php
  $landingJsFile = resource_path('js/landing/site.js');
  if (! is_file($landingJsFile)) {
      $landingJsFile = public_path('js/landing/site.js');
  }
  $landingJsVer = is_file($landingJsFile) ? (string) filemtime($landingJsFile) : (string) time();
@endphp
<script src="{{ route('assets.landing.js', ['file' => 'site']) }}?v={{ $landingJsVer }}" defer></script>
@if($fawaterakActive && ! $fawaterakMis && $fawaterakIntegration === 'iframe')
<script>
(function(){
    var prepareUrl = @json(route('public.service-packages.fawaterak.prepare', $package));
    var meta = document.querySelector('meta[name="csrf-token"]');
    var token = (meta && meta.getAttribute('content')) || @json(csrf_token());
    var errEl = document.getElementById('fawaterk-checkout-error');
    function showErr(msg) {
        if (!errEl) { alert(msg); return; }
        errEl.textContent = msg;
        errEl.classList.remove('hidden');
    }
    function waitForFawaterkFn(resolve, reject) {
        window.requestAnimationFrame(function() {
            if (typeof fawaterkCheckout === 'function') { resolve(); }
            else {
                setTimeout(function() {
                    if (typeof fawaterkCheckout === 'function') resolve();
                    else reject(new Error('no_fn'));
                }, 80);
            }
        });
    }
    function loadScriptTag(url) {
        return new Promise(function(resolve, reject) {
            var s = document.createElement('script');
            s.src = url; s.async = true;
            s.onload = function() { waitForFawaterkFn(resolve, reject); };
            s.onerror = function() { reject(new Error('network')); };
            document.head.appendChild(s);
        });
    }
    function loadScriptViaBlob(url) {
        return fetch(url, { credentials: 'same-origin', cache: 'no-store' })
            .then(function(r) {
                if (!r.ok) throw new Error('fetch ' + r.status);
                return r.text();
            })
            .then(function(code) {
                if (!code || code.trim().indexOf('<') === 0) throw new Error('not_js');
                var blob = new Blob([code], { type: 'application/javascript' });
                var blobUrl = URL.createObjectURL(blob);
                return new Promise(function(resolve, reject) {
                    var s = document.createElement('script');
                    s.onload = function() { URL.revokeObjectURL(blobUrl); waitForFawaterkFn(resolve, reject); };
                    s.onerror = function() { URL.revokeObjectURL(blobUrl); reject(new Error('blob_load')); };
                    s.src = blobUrl;
                    document.head.appendChild(s);
                });
            });
    }
    function loadScript(src) {
        var sep = src.indexOf('?') >= 0 ? '&' : '?';
        var url = src + sep + '_fk=' + Date.now();
        return loadScriptTag(url).catch(function() { return loadScriptViaBlob(url); });
    }
    function parseJsonSafe(text) { try { return JSON.parse(text); } catch (e) { return null; } }
    function run() {
        var fd = new FormData();
        fd.append('_token', token);
        fetch(prepareUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: fd,
            credentials: 'same-origin'
        })
        .then(function(r) {
            return r.text().then(function(text) {
                return { ok: r.ok, status: r.status, data: parseJsonSafe(text) };
            });
        })
        .then(function(res) {
            if (res.status === 401) { showErr(@json($isRtl ? 'انتهت الجلسة. سجّل الدخول ثم أعد فتح الصفحة.' : 'Session expired. Sign in and reopen this page.')); return; }
            if (res.status === 419) { showErr(@json($isRtl ? 'انتهت صلاحية الجلسة. حدّث الصفحة (F5).' : 'Session expired. Refresh the page.')); return; }
            if (!res.data || !res.ok) {
                showErr((res.data && res.data.message) || @json($isRtl ? 'تعذّر تجهيز الطلب.' : 'Could not prepare checkout.'));
                return;
            }
            if (res.data.mode === 'completed' && res.data.redirect) {
                window.location.href = res.data.redirect;
                return;
            }
            if (res.data.mode !== 'iframe' || !res.data.pluginScriptUrl || !res.data.pluginConfig) {
                showErr(@json($isRtl ? 'استجابة غير متوقعة من بوابة الدفع.' : 'Unexpected payment gateway response.'));
                return;
            }
            return loadScript(res.data.pluginScriptUrl).then(function() {
                fawaterkCheckout(res.data.pluginConfig);
            });
        })
        .catch(function() {
            showErr(@json($isRtl ? 'تعذّر تحميل بوابة فواتيرك. حدّث الصفحة أو جرّب متصفحاً آخر.' : 'Could not load Fawaterak. Refresh or try another browser.'));
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run);
    else run();
})();
</script>
@endif
@if($fawaterakActive && ! $fawaterakMis && $fawaterakIntegration === 'api')
<script>
(function(){
    var prepareUrl = @json(route('public.service-packages.fawaterak.prepare', $package));
    var methodsUrl = @json(route('public.service-packages.fawaterak.methods', $package));
    var payUrl = @json(route('public.service-packages.fawaterak.pay', $package));
    var meta = document.querySelector('meta[name="csrf-token"]');
    var token = (meta && meta.getAttribute('content')) || @json(csrf_token());
    var errEl = document.getElementById('fawaterk-api-error');
    var loadEl = document.getElementById('fawaterk-api-loading');
    var methodsEl = document.getElementById('fawaterk-api-methods');
    var payBtn = document.getElementById('fawaterk-api-pay-btn');
    var resultEl = document.getElementById('fawaterk-api-result');
    var walletWrap = document.getElementById('fawaterk-api-wallet-wrap');
    var walletInput = document.getElementById('fawaterk-api-wallet');
    var selectedId = null;
    function showErr(msg) {
        if (!errEl) { alert(msg); return; }
        errEl.textContent = msg;
        errEl.classList.remove('hidden');
    }
    function parseJsonSafe(text) { try { return JSON.parse(text); } catch (e) { return null; } }
    function renderMethods(list) {
        if (!methodsEl) return;
        methodsEl.innerHTML = '';
        list.forEach(function(m) {
            var id = m.paymentId;
            var name = (document.documentElement.getAttribute('dir') === 'rtl' && m.name_ar) ? m.name_ar : (m.name_en || m.name_ar || ('#' + id));
            var card = document.createElement('button');
            card.type = 'button';
            card.style.cssText = 'display:flex;align-items:center;gap:.65rem;padding:.75rem;border-radius:12px;border:2px solid #D7DDE6;background:#fff;text-align:start;cursor:pointer;font:700 .82rem Tajawal,sans-serif;color:#0B1220';
            card.setAttribute('data-pid', String(id));
            if (m.logo && typeof m.logo === 'string') {
                var img = document.createElement('img');
                img.src = m.logo; img.alt = ''; img.style.cssText = 'height:2.25rem;width:auto;object-fit:contain;flex-shrink:0';
                img.loading = 'lazy';
                card.appendChild(img);
            }
            var title = document.createElement('span');
            title.textContent = name;
            card.appendChild(title);
            card.addEventListener('click', function() {
                methodsEl.querySelectorAll('button').forEach(function(b) { b.style.borderColor = '#D7DDE6'; });
                card.style.borderColor = '#F5B800';
                selectedId = id;
                if (payBtn) payBtn.disabled = false;
            });
            methodsEl.appendChild(card);
        });
        methodsEl.classList.remove('hidden');
        if (walletWrap) walletWrap.classList.remove('hidden');
    }
    function showPaymentResult(pd) {
        if (!resultEl || !pd) return;
        resultEl.classList.remove('hidden');
        if (pd.redirectTo) { window.location.href = pd.redirectTo; return; }
        var html = '';
        if (pd.fawryCode) html += '<p><strong>رمز فوري:</strong> <span dir="ltr">' + pd.fawryCode + '</span></p>';
        if (pd.expireDate) html += '<p>{{ $isRtl ? "ينتهي" : "Expires" }}: ' + pd.expireDate + '</p>';
        if (!html) html = '<pre style="font-size:.72rem;white-space:pre-wrap;word-break:break-all" dir="ltr">' + JSON.stringify(pd, null, 2) + '</pre>';
        resultEl.innerHTML = html;
    }
    function run() {
        var fd = new FormData();
        fd.append('_token', token);
        fetch(prepareUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: fd,
            credentials: 'same-origin'
        })
        .then(function(r) { return r.text().then(function(t) { return { ok: r.ok, status: r.status, data: parseJsonSafe(t) }; }); })
        .then(function(res) {
            if (res.status === 401 || res.status === 419) { showErr(@json($isRtl ? 'انتهت الجلسة. حدّث الصفحة.' : 'Session expired. Refresh.')); return; }
            if (!res.data || !res.ok) { showErr((res.data && res.data.message) || @json($isRtl ? 'تعذّر تجهيز الطلب.' : 'Could not prepare checkout.')); return; }
            if (res.data.mode !== 'api') { showErr(@json($isRtl ? 'الخادم ليس في وضع API.' : 'Server is not in API mode.')); return; }
            return fetch(methodsUrl, {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
        })
        .then(function(r) {
            if (!r) return;
            return r.text().then(function(t) { return { ok: r.ok, data: parseJsonSafe(t) }; });
        })
        .then(function(res) {
            if (!res) return;
            if (loadEl) loadEl.classList.add('hidden');
            if (!res.ok || !res.data || res.data.status !== 'success' || !Array.isArray(res.data.data)) {
                showErr((res.data && res.data.message) || @json($isRtl ? 'تعذّر جلب وسائل الدفع.' : 'Could not load payment methods.'));
                return;
            }
            renderMethods(res.data.data);
        })
        .catch(function() {
            if (loadEl) loadEl.classList.add('hidden');
            showErr(@json($isRtl ? 'تعذّر الاتصال بالخادم.' : 'Could not reach the server.'));
        });
    }
    if (payBtn) {
        payBtn.addEventListener('click', function() {
            if (!selectedId) return;
            if (errEl) errEl.classList.add('hidden');
            payBtn.disabled = true;
            var body = { payment_method_id: selectedId };
            var w = walletInput && walletInput.value ? walletInput.value.trim() : '';
            if (w) body.mobile_wallet_number = w;
            fetch(payUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify(body)
            })
            .then(function(r) { return r.text().then(function(t) { return { ok: r.ok, data: parseJsonSafe(t) }; }); })
            .then(function(res) {
                payBtn.disabled = false;
                if (!res.data || !res.ok) {
                    showErr((res.data && res.data.message) || @json($isRtl ? 'تعذّر بدء الدفع.' : 'Could not start payment.'));
                    return;
                }
                var pd = res.data.data && res.data.data.payment_data;
                showPaymentResult(pd);
            })
            .catch(function() {
                payBtn.disabled = false;
                showErr(@json($isRtl ? 'تعذّر الاتصال بالخادم.' : 'Could not reach the server.'));
            });
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run);
    else run();
})();
</script>
@endif
</body>
</html>
