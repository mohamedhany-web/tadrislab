@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'TADRIS LAB');
    $application = $application ?? null;
    $waitStatus = $waitStatus ?? ($application->status ?? 'pending');
    $isApproved = $waitStatus === \App\Models\TutorApplication::STATUS_APPROVED;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
  <title>{{ $isApproved
    ? ($isRtl ? 'بانتظار تفعيل الإدارة' : 'Waiting for admin activation')
    : ($isRtl ? 'طلبك قيد المراجعة' : 'Application under review') }} — {{ $brand }}</title>
  <meta name="theme-color" content="#0B3D91">
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme', 'instructor-profile']])
</head>
<body class="sana-home sana-courses-page ta-page ta-page--status">
@include('partials.landing.navbar', ['navActive' => null, 'navSolid' => true, 'navHero' => false])

<main class="ta-status">
  <div class="ta-status__glow" aria-hidden="true"></div>

  @if(session('success'))
    <div class="ta-ok ta-status__flash">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="ta-note ta-status__flash" style="background:#FEF2F2;border-color:#FECACA;color:#991B1B">{{ session('error') }}</div>
  @endif

  <section class="ta-status__panel">
    <div class="ta-status__icon" aria-hidden="true">
      <svg viewBox="0 0 64 64" width="56" height="56" fill="none">
        <circle cx="32" cy="32" r="30" stroke="#F5B800" stroke-width="3" opacity=".35"/>
        <circle cx="32" cy="32" r="22" fill="#FFF8E6"/>
        <path d="M32 18v16l10 6" stroke="#0B3D91" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <p class="ta-status__chip">{{ $isRtl ? 'توظيف المعلمين' : 'Teacher hiring' }}</p>
    <h1 class="ta-status__title">
      {{ $isApproved
        ? ($isRtl ? 'تم قبول طلبك — بانتظار التفعيل' : 'Accepted — waiting for activation')
        : ($isRtl ? 'طلبك قيد المراجعة' : 'Your application is under review') }}
    </h1>
    <p class="ta-status__lead">
      {{ $isApproved
        ? ($isRtl
          ? 'قبلنا ملفك. لوحة المعلم ولوحات التحكم تُفتح بعد خطوة التفعيل من الإدارة. سجّل الدخول لاحقاً بنفس الإيميل وكلمة المرور.'
          : 'Your profile was accepted. Dashboards open only after admin activation. Log in later with the same email and password.')
        : ($isRtl
          ? 'استلمنا بياناتك. لن تتمكن من دخول لوحة المعلم حتى تراجع الإدارة طلبك ثم تفعّل الحساب.'
          : 'We received your details. You cannot open the instructor dashboard until admin review and activation.') }}
    </p>

    @if($application)
      <div class="ta-status__meta">
        <span>{{ $application->full_name }}</span>
        <span dir="ltr">{{ $application->email }}</span>
        <span class="ta-status__badge">{{ $isApproved ? ($isRtl ? 'بانتظار التفعيل' : 'Awaiting activation') : ($isRtl ? 'قيد المراجعة' : 'Pending review') }}</span>
      </div>
    @endif

    <ol class="ta-status__steps">
      <li class="is-done">
        <span class="ta-status__step-dot" aria-hidden="true"></span>
        <div>
          <strong>{{ $isRtl ? 'تم إنشاء الحساب' : 'Account created' }}</strong>
          <p>{{ $isRtl ? 'يمكنك تسجيل الدخول لإكمال الملف أو متابعة الحالة.' : 'You can log in to complete the profile or check status.' }}</p>
        </div>
      </li>
      <li class="{{ $isApproved ? 'is-done' : 'is-current' }}">
        <span class="ta-status__step-dot" aria-hidden="true"></span>
        <div>
          <strong>{{ $isRtl ? 'مراجعة الإدارة' : 'Admin review' }}</strong>
          <p>{{ $isRtl ? 'نراجع البيانات والمستندات قبل القبول.' : 'We review details and documents before approval.' }}</p>
        </div>
      </li>
      <li class="{{ $isApproved ? 'is-current' : '' }}">
        <span class="ta-status__step-dot" aria-hidden="true"></span>
        <div>
          <strong>{{ $isRtl ? 'تفعيل الحساب ولوحة المعلم' : 'Activation & dashboard' }}</strong>
          <p>{{ $isRtl ? 'بعد التفعيل يظهر ملفك للطلاب وتُفتح لوحة التحكم.' : 'After activation your public profile appears and the dashboard unlocks.' }}</p>
        </div>
      </li>
    </ol>

    <div class="ta-status__actions">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sana-btn sana-btn--outline sana-btn--lg">
          {{ $isRtl ? 'تسجيل الخروج' : 'Log out' }}
        </button>
      </form>
    </div>
  </section>
</main>

@include('partials.landing.footer')
</body>
</html>
