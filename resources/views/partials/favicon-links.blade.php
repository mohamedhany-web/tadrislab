{{-- أيقونة التبويب: شعار لوحة التحكم إن وُجد، وإلا لوجو تدريس لاب الرسمي --}}
@php
    $brandIcon = \App\Services\AdminPanelBranding::logoPublicUrl();
    $fallbackIcon = lasles_img('logo-mark.png');
    $icon = $brandIcon ?: $fallbackIcon;
@endphp
<link rel="icon" href="{{ $icon }}" sizes="any">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
<link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">
