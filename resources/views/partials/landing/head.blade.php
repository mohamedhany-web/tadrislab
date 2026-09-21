{{-- رأس صفحات اللاندنج العامة — Lasles / TADRIS LAB --}}
@php
    $landingCss = $landingCss ?? [];
    // Always load Lasles chrome first; optional legacy content sheets after
    $sheets = array_values(array_unique(array_merge(['lasles'], $landingCss)));
    $themeColor = '#1E4E8C';
@endphp
<script>document.documentElement.classList.add('js');</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta name="theme-color" content="{{ $themeColor }}">
@foreach($sheets as $sheet)
  @php
      $landingCssFile = public_path('css/landing/'.$sheet.'.css');
      if (! is_file($landingCssFile)) {
          $landingCssFile = resource_path('css/landing/'.$sheet.'.css');
      }
      $landingCssVer = is_file($landingCssFile) ? (string) filemtime($landingCssFile) : (string) time();
  @endphp
  <link rel="stylesheet" href="{{ route('assets.landing.css', ['sheet' => $sheet]) }}?v={{ $landingCssVer }}">
@endforeach
<style>
  :root {
    --p: #1E4E8C;
    --p-dark: #184888;
    --p-deep: #152A4A;
    --p-light: #3B7BC4;
    --p-glow: #4A8AD0;
    --gold: #A88050;
    --gold-dark: #8F6B3F;
  }
  /* Soft bridge: old sana page shells under Lasles chrome */
  body.sana-home {
    font-family: "Rubik", system-ui, sans-serif;
    background: #fff;
    color: #1A3558;
  }
  body.sana-home .sana-cat-page,
  body.sana-home main {
    padding-top: 0.5rem;
  }
</style>
