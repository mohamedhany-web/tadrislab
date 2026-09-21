@extends('layouts.student-timeline')

@section('title', 'مساعد المعلم')

@section('content')
@php
  $isRtl = app()->getLocale() === 'ar';
  $input = $input ?? ['lesson_name' => old('lesson_name'), 'subject' => old('subject'), 'grade' => old('grade')];
@endphp
<div class="st-page" style="padding:1.5rem;max-width:52rem">
  <header style="margin-bottom:1.5rem">
    <h1 style="margin:0 0 .35rem;font-size:1.5rem;font-weight:800;color:#152A4A">{{ $isRtl ? 'مساعد المعلم' : 'Teacher Assistant' }}</h1>
    <p style="margin:0;color:#64748b;line-height:1.6">
      {{ $isRtl
        ? 'أدخل اسم الدرس (ومادة/صف اختياريًا) لتحصل على أفكار عملية للتهيئة والافتتاح والاستراتيجية والنشاط والتقويم.'
        : 'Enter a lesson name (optional subject/grade) for practical classroom delivery ideas.' }}
    </p>
    <p style="margin:.5rem 0 0;font-size:.75rem;color:#94a3b8">
      {{ $isRtl ? 'المزوّد:' : 'Provider:' }} {{ $provider }}
      · {{ $isRtl ? 'عقد AI-ready — يمكن ربط محرك ذكاء لاحقًا دون إعادة بناء الشاشة.' : 'AI-ready contract — swap engines later without rebuilding the UI.' }}
    </p>
  </header>

  @if($errors->any())
    <div style="margin-bottom:1rem;padding:1rem;border:1px solid #f5c2c7;background:#f8d7da;border-radius:12px;font-size:.9rem">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('student.teacher-assistant.generate') }}" style="display:grid;gap:1rem;padding:1.25rem;border:1px solid #e2e8f0;border-radius:14px;background:#fff;margin-bottom:1.5rem">
    @csrf
    <div>
      <label for="lesson_name" style="display:block;font-size:.8rem;font-weight:700;margin-bottom:.35rem;color:#334155">{{ $isRtl ? 'اسم الدرس / موضوع الممارسة *' : 'Lesson / practice topic *' }}</label>
      <input id="lesson_name" name="lesson_name" required value="{{ $input['lesson_name'] ?? '' }}"
             placeholder="{{ $isRtl ? 'مثال: إدارة بداية الحصة' : 'e.g. Opening routines' }}"
             style="width:100%;padding:.7rem .9rem;border:1px solid #cbd5e1;border-radius:10px">
    </div>
    <div style="display:grid;gap:1rem;grid-template-columns:1fr 1fr">
      <div>
        <label for="subject" style="display:block;font-size:.8rem;font-weight:700;margin-bottom:.35rem;color:#334155">{{ $isRtl ? 'المادة (اختياري)' : 'Subject (optional)' }}</label>
        <input id="subject" name="subject" value="{{ $input['subject'] ?? '' }}" style="width:100%;padding:.7rem .9rem;border:1px solid #cbd5e1;border-radius:10px">
      </div>
      <div>
        <label for="grade" style="display:block;font-size:.8rem;font-weight:700;margin-bottom:.35rem;color:#334155">{{ $isRtl ? 'الصف (اختياري)' : 'Grade (optional)' }}</label>
        <input id="grade" name="grade" value="{{ $input['grade'] ?? '' }}" style="width:100%;padding:.7rem .9rem;border:1px solid #cbd5e1;border-radius:10px">
      </div>
    </div>
    <button type="submit" style="justify-self:start;padding:.7rem 1.25rem;background:#1E4E8C;color:#fff;border:0;border-radius:999px;font-weight:700;cursor:pointer">
      {{ $isRtl ? 'ولّد الأفكار' : 'Generate ideas' }}
    </button>
  </form>

  @if(!empty($result))
    <section style="display:grid;gap:1rem">
      @foreach($labels as $key => $meta)
        @php
          $label = is_array($meta) ? ($isRtl ? ($meta['ar'] ?? $key) : ($meta['en'] ?? $key)) : (string) $meta;
          $text = $result[$key] ?? null;
        @endphp
        @if($text)
          <article style="padding:1.1rem 1.25rem;border:1px solid #e2e8f0;border-radius:14px;background:#fff">
            <h2 style="margin:0 0 .5rem;font-size:.95rem;font-weight:800;color:#1E4E8C">{{ $label }}</h2>
            <p style="margin:0;line-height:1.7;color:#334155;white-space:pre-wrap">{{ $text }}</p>
          </article>
        @endif
      @endforeach
    </section>
  @endif
</div>
@endsection
