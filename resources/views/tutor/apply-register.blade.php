@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'TADRIS LAB');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $isRtl ? 'إنشاء حساب معلّم' : 'Create teacher account' }} — {{ $brand }}</title>
  <meta name="theme-color" content="#0B3D91">
  <link rel="canonical" href="{{ route('public.tutor.apply') }}">
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme', 'instructor-profile']])
</head>
<body class="sana-home sana-courses-page ta-page">
@include('partials.landing.navbar', ['navActive' => null, 'navSolid' => true, 'navHero' => false])

<main class="ta-wrap">
  <p class="ta-chip">{{ $isRtl ? 'الخطوة 1 من 2' : 'Step 1 of 2' }}</p>
  <h1 class="ta-title">{{ $isRtl ? 'أنشئ حسابك كمعلّم' : 'Create your teacher account' }}</h1>
  <p class="ta-sub">
    {{ $isRtl
      ? 'سجّل بالإيميل وكلمة المرور ثم أكمل الملف التعريفي. لوحة المعلم تُفتح فقط بعد تفعيل الإدارة.'
      : 'Register with email and password, then complete your profile. The instructor dashboard opens only after admin activation.' }}
  </p>

  @if(session('success'))
    <div class="ta-ok">{{ session('success') }}</div>
  @endif

  <div class="ta-note">
    {{ $isRtl
      ? 'هذا الإيميل وكلمة المرور هما بيانات دخولك. بعد التسجيل تكمل الملف، وبعد تفعيل الإدارة تُفتح لوحة المعلم.'
      : 'This email and password are your login credentials. After signup you complete your profile; the dashboard unlocks after admin activation.' }}
  </div>

  <form method="POST" action="{{ route('public.tutor.apply.register') }}" class="ta-card">
    @csrf
    <h2>{{ $isRtl ? 'بيانات الدخول' : 'Login details' }}</h2>
    <div class="ta-grid">
      <div class="ta-field">
        <label for="full_name">{{ $isRtl ? 'الاسم الكامل' : 'Full name' }} <span class="req">*</span></label>
        <input id="full_name" type="text" name="full_name" value="{{ old('full_name') }}" required autocomplete="name">
        @error('full_name')<p class="ta-err">{{ $message }}</p>@enderror
      </div>
      <div class="ta-field">
        <label for="email">{{ $isRtl ? 'البريد الإلكتروني' : 'Email' }} <span class="req">*</span></label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required dir="ltr" autocomplete="email">
        @error('email')<p class="ta-err">{{ $message }}</p>@enderror
      </div>
      <div class="ta-field">
        <label for="phone">{{ $isRtl ? 'رقم الجوال / واتساب' : 'Phone / WhatsApp' }} <span class="req">*</span></label>
        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required dir="ltr" autocomplete="tel" placeholder="+9665xxxxxxxx">
        @error('phone')<p class="ta-err">{{ $message }}</p>@enderror
      </div>
      <div class="ta-grid ta-grid--2">
        <div class="ta-field">
          <label for="password">{{ $isRtl ? 'كلمة المرور' : 'Password' }} <span class="req">*</span></label>
          <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password">
          @error('password')<p class="ta-err">{{ $message }}</p>@enderror
        </div>
        <div class="ta-field">
          <label for="password_confirmation">{{ $isRtl ? 'تأكيد كلمة المرور' : 'Confirm password' }} <span class="req">*</span></label>
          <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
        </div>
      </div>
    </div>
    <button type="submit" class="sana-btn sana-btn--yellow sana-btn--lg" style="width:100%;justify-content:center;margin-top:1rem">
      {{ $isRtl ? 'إنشاء الحساب والمتابعة' : 'Create account & continue' }}
    </button>
    <p class="ta-hint" style="margin-top:.85rem;text-align:center">
      {{ $isRtl ? 'لديك حساب؟' : 'Already have an account?' }}
      <a href="{{ route('login', ['redirect' => route('public.tutor.apply.profile')]) }}" style="color:#0B3D91;font-weight:800">{{ $isRtl ? 'تسجيل الدخول' : 'Log in' }}</a>
    </p>
  </form>
</main>

@include('partials.landing.footer')
</body>
</html>
