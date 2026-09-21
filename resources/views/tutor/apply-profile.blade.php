@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'TADRIS LAB');
    $application = $application ?? null;
    $form = $form ?? null;
    $fields = $fields ?? collect();
    $oldAnswers = old('answers', []);
    $saved = is_array($application->answers ?? null) ? $application->answers : [];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $form->title ?? ($isRtl ? 'إكمال بيانات المعلم' : 'Complete teacher profile') }} — {{ $brand }}</title>
  <meta name="theme-color" content="#0B3D91">
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => ['theme', 'instructor-profile']])
</head>
<body class="sana-home sana-courses-page ta-page">
@include('partials.landing.navbar', ['navActive' => null, 'navSolid' => true, 'navHero' => false])

<main class="ta-wrap">
  <p class="ta-chip">{{ $isRtl ? 'الخطوة 2 من 2' : 'Step 2 of 2' }}</p>
  <h1 class="ta-title">{{ $form->title ?? ($isRtl ? 'أكمل بياناتك' : 'Complete your details') }}</h1>
  <p class="ta-sub">
    {{ $form->description ?: ($isRtl
      ? 'حسابك جاهز: '.($user->email ?? '').' — أكمل الملف التعريفي ثم أرسله للإدارة. لوحة المعلم لا تُفتح إلا بعد التفعيل.'
      : 'Your account is ready: '.($user->email ?? '').' — complete your profile and submit it. The dashboard opens only after admin activation.') }}
  </p>

  @if(session('success'))
    <div class="ta-ok">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="ta-note" style="background:#FEF2F2;border-color:#FECACA;color:#991B1B">{{ session('error') }}</div>
  @endif
  @error('form')
    <div class="ta-note" style="background:#FEF2F2;border-color:#FECACA;color:#991B1B">{{ $message }}</div>
  @enderror
  @error('intro_video')
    <div class="ta-note" style="background:#FEF2F2;border-color:#FECACA;color:#991B1B">{{ $message }}</div>
  @enderror
  @error('phone')
    <div class="ta-note" style="background:#FEF2F2;border-color:#FECACA;color:#991B1B">{{ $message }}</div>
  @enderror

  <form method="POST" action="{{ route('public.tutor.apply.profile.store') }}" enctype="multipart/form-data">
    @csrf

    @foreach($fields as $field)
      @if($field->isSection())
        <div class="ta-card" style="background:linear-gradient(135deg,#EEF3FB,#fff);border-style:dashed">
          <h2 style="margin:0">{{ $field->label }}</h2>
          @if($field->help_text)<p class="ta-hint" style="margin-top:.35rem">{{ $field->help_text }}</p>@endif
        </div>
        @continue
      @endif

      @php
        $fid = $field->id;
        $savedVal = $saved[(string)$fid]['value'] ?? null;
        if ($savedVal === null && $field->system_key) {
          $savedVal = $application->{$field->system_key} ?? null;
          if ($field->system_key === 'photo') $savedVal = null;
        }
        $value = $oldAnswers[$fid] ?? $savedVal;
      @endphp

      <section class="ta-card">
        <div class="ta-field">
          <label for="field_{{ $fid }}">
            {{ $field->label }}
            @if($field->is_required)<span class="req">*</span>@endif
          </label>
          @if($field->help_text)<p class="ta-hint">{{ $field->help_text }}</p>@endif

          @switch($field->type)
            @case('long_text')
              <textarea id="field_{{ $fid }}" name="answers[{{ $fid }}]" @if($field->is_required) required @endif placeholder="{{ $field->placeholder }}">{{ $value }}</textarea>
              @break
            @case('email')
              <input id="field_{{ $fid }}" type="email" name="answers[{{ $fid }}]" value="{{ $value }}" @if($field->is_required) required @endif dir="ltr" placeholder="{{ $field->placeholder }}">
              @break
            @case('phone')
              <input id="field_{{ $fid }}" type="tel" name="answers[{{ $fid }}]" value="{{ $value ?? $application->phone }}" @if($field->is_required) required @endif dir="ltr" placeholder="{{ $field->placeholder ?: '+9665...' }}">
              @break
            @case('number')
              <input id="field_{{ $fid }}" type="number" name="answers[{{ $fid }}]" value="{{ $value }}" @if($field->is_required) required @endif placeholder="{{ $field->placeholder }}">
              @break
            @case('date')
              <input id="field_{{ $fid }}" type="date" name="answers[{{ $fid }}]" value="{{ $value }}" @if($field->is_required) required @endif>
              @break
            @case('url')
              <input id="field_{{ $fid }}" type="url" name="answers[{{ $fid }}]" value="{{ $value }}" @if($field->is_required) required @endif dir="ltr" placeholder="{{ $field->placeholder ?: 'https://' }}">
              @break
            @case('select')
              <select id="field_{{ $fid }}" name="answers[{{ $fid }}]" @if($field->is_required) required @endif>
                <option value="">{{ $isRtl ? '— اختر —' : '— choose —' }}</option>
                @foreach($field->options ?? [] as $opt)
                  @php $ov = is_array($opt) ? ($opt['value'] ?? '') : $opt; $ol = is_array($opt) ? ($opt['label'] ?? $ov) : $opt; @endphp
                  <option value="{{ $ov }}" @selected((string)$value === (string)$ov)>{{ $ol }}</option>
                @endforeach
              </select>
              @break
            @case('radio')
              <div class="ta-grid" style="gap:.45rem">
                @foreach($field->options ?? [] as $opt)
                  @php $ov = is_array($opt) ? ($opt['value'] ?? '') : $opt; $ol = is_array($opt) ? ($opt['label'] ?? $ov) : $opt; @endphp
                  <label style="display:flex;align-items:center;gap:.5rem;font:700 .88rem Tajawal,sans-serif;color:#334155">
                    <input type="radio" name="answers[{{ $fid }}]" value="{{ $ov }}" @checked((string)$value === (string)$ov) @if($field->is_required) required @endif>
                    {{ $ol }}
                  </label>
                @endforeach
              </div>
              @break
            @case('checkbox')
              @php $arr = is_array($value) ? $value : (filled($value) ? [$value] : []); @endphp
              <div class="ta-grid" style="gap:.45rem">
                @foreach($field->options ?? [] as $opt)
                  @php $ov = is_array($opt) ? ($opt['value'] ?? '') : $opt; $ol = is_array($opt) ? ($opt['label'] ?? $ov) : $opt; @endphp
                  <label style="display:flex;align-items:center;gap:.5rem;font:700 .88rem Tajawal,sans-serif;color:#334155">
                    <input type="checkbox" name="answers[{{ $fid }}][]" value="{{ $ov }}" @checked(in_array((string)$ov, array_map('strval', $arr), true))>
                    {{ $ol }}
                  </label>
                @endforeach
              </div>
              @break
            @case('file')
              @php
                $existingPath = $saved[(string)$fid]['path'] ?? null;
                if (! $existingPath && $field->system_key === 'photo') $existingPath = $application->photo_path;
                if (! $existingPath && $field->system_key === 'id_document') $existingPath = $application->id_document_path;
                if (! $existingPath && $field->system_key === 'certificate') $existingPath = $application->certificate_path;
                if (! $existingPath && $field->system_key === 'intro_video') $existingPath = $application->intro_video_path;
              @endphp
              @if($existingPath)
                <p class="ta-hint" style="color:#065F46">{{ $isRtl ? 'تم الرفع مسبقاً — يمكنك استبداله بملف جديد.' : 'Already uploaded — you can replace it.' }}</p>
              @endif
              <input id="field_{{ $fid }}" type="file" name="hiring_upload[{{ $fid }}]" accept="{{ $field->fileAccept() }}" @if($field->is_required && ! $existingPath) required @endif>
              @break
            @default
              <input id="field_{{ $fid }}" type="text" name="answers[{{ $fid }}]" value="{{ $value }}" @if($field->is_required) required @endif placeholder="{{ $field->placeholder }}">
          @endswitch

          @error('answers.'.$fid)<p class="ta-err">{{ $message }}</p>@enderror
          @error('hiring_upload.'.$fid)<p class="ta-err">{{ $message }}</p>@enderror
        </div>
      </section>
    @endforeach

    <button type="submit" class="sana-btn sana-btn--yellow sana-btn--lg" style="width:100%;justify-content:center;margin-top:.5rem">
      {{ $isRtl ? 'إرسال للمراجعة' : 'Submit for review' }}
    </button>
  </form>
</main>

@include('partials.landing.footer')
</body>
</html>
