@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'TADRIS LAB');
    $name = $profile->user->name ?? __('public.instructor_fallback');
    $headline = $profile->headline_clean ?: __('public.instructor_fallback');
    $bioClean = $profile->bio_clean;
    $skills = $profile->skills_list ?? [];
    $experiences = $profile->experience_list ?? [];
    $instrPageTitle = $name.' — '.$headline.' | '.$brand;
    $instrPageDesc = \Illuminate\Support\Str::limit($bioClean ?: $headline, 160);
    $instrPageImg = ($profile->photo_url ?? null) ?: asset('images/og-image.jpg');
    $instrPageUrl = route('public.instructors.show', $profile->user);
    $weeklyCalendar = $weeklyCalendar ?? [];
    $canBook = (bool) ($canBook ?? false);
    $unitsLeft = (int) ($unitsLeft ?? 0);
    $bookableSlots = $bookableSlots ?? collect();
    $packagesUrl = $packagesUrl ?? route('public.service-packages.index');
    $introEmbedUrl = $introEmbedUrl ?? null;
    $introDirectVideo = $introDirectVideo ?? null;
    $hasIntroVideo = filled($introEmbedUrl) || filled($introDirectVideo);
    $oneToOneCourses = $oneToOneCourses ?? collect();
    $privateGroups = $privateGroups ?? collect();
    $groupCourses = $groupCourses ?? collect();
    $skillChips = [];
    $skillNotes = [];
    foreach ($skills as $skill) {
        $skill = trim((string) $skill);
        if ($skill === '') {
            continue;
        }
        if (mb_strlen($skill) > 42 || substr_count($skill, ' ') > 6) {
            $skillNotes[] = $skill;
        } else {
            $skillChips[] = $skill;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $instrPageTitle }}</title>
  <meta name="description" content="{{ $instrPageDesc }}">
  <meta name="theme-color" content="#0B3D91">
  <link rel="canonical" href="{{ $instrPageUrl }}">
  <meta property="og:type" content="profile">
  <meta property="og:url" content="{{ $instrPageUrl }}">
  <meta property="og:title" content="{{ $instrPageTitle }}">
  <meta property="og:description" content="{{ $instrPageDesc }}">
  <meta property="og:image" content="{{ $instrPageImg }}">
  <meta property="og:site_name" content="{{ $brand }}">
  @include('partials.favicon-links')
  @include('partials.seo-jsonld', ['jsonldType' => 'instructor', 'profile' => $profile])
  @include('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog', 'instructors-catalog', 'instructor-profile']])
</head>
<body class="sana-home sana-courses-page sana-instructors-page gl-tp">
<div id="sana-scroll-progress"></div>
@include('partials.landing.navbar', ['navActive' => 'instructors', 'navSolid' => true, 'navHero' => false])

<main class="sana-cat-page">
  <section class="sana-cat-hero gl-tp-hero" id="cat-hero">
    <div class="sana-cat-hero__dots"></div>
    <div class="sana-container sana-cat-hero__inner">
      <nav class="sana-cat-hero__breadcrumb" aria-label="{{ $isRtl ? 'مسار التنقل' : 'Breadcrumb' }}">
        <a href="{{ url('/') }}">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a>
        <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>
        <a href="{{ route('public.instructors.index') }}">{{ __('landing.nav.instructors') }}</a>
        <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>
        <span>{{ $name }}</span>
      </nav>

      <div class="gl-tp-hero__row">
        <div class="gl-tp-hero__ring">
          @if($profile->photo_url)
            <img src="{{ $profile->photo_url }}" alt="{{ $name }}">
          @else
            <span class="av" aria-hidden="true">{{ mb_substr($name, 0, 1) }}</span>
          @endif
        </div>
        <div class="gl-tp-hero__copy">
          <span class="sana-inst-hero__eyebrow"><i class="fas fa-circle-check"></i> {{ __('public.instructors_verified') }}</span>
          <h1 class="sana-cat-hero__title">{{ $name }}</h1>
          @if($headline !== '' && $headline !== $name)
            <p class="gl-tp-hero__headline">{{ $headline }}</p>
          @endif
          @if($hasIntroVideo)
            <div class="sana-inst-hero__actions">
              <button type="button" class="sana-btn sana-btn--yellow sana-btn--sm" id="glTpIntroOpen" aria-haspopup="dialog" aria-controls="glTpIntroModal">
                <i class="fas fa-play"></i>
                {{ $isRtl ? 'فيديو تعريفي' : 'Intro video' }}
              </button>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>

  <div class="sana-container gl-tp-wrap">
    @if($errors->any())
      <div class="gl-tp-note is-err">{{ $errors->first() }}</div>
    @endif
    @if(session('success'))
      <div class="gl-tp-note is-ok">{{ session('success') }}</div>
    @endif

    <div class="gl-tp-layout">
      <div class="gl-tp-stack">
        <article class="gl-tp-card gl-tp-about">
          <h2>{{ __('public.instructor_bio_title') }}</h2>
          @if($bioClean)
            <p class="gl-tp-bio">{{ $bioClean }}</p>
          @else
            <p class="gl-tp-bio">{{ $isRtl ? 'لا توجد نبذة منشورة بعد.' : 'No published bio yet.' }}</p>
          @endif
        </article>

        @if($oneToOneCourses->isNotEmpty() || $privateGroups->isNotEmpty())
          <section class="gl-tp-private" id="private-lessons" aria-labelledby="glTpPrivateTitle">
            <div class="gl-tp-private__head">
              <h2 id="glTpPrivateTitle">{{ $isRtl ? 'مع هذا المعلم' : 'With this teacher' }}</h2>
              <p>{{ $isRtl ? 'كورسات وعروض مرتبطة بهذا المعلم' : 'Courses and offerings linked to this teacher' }}</p>
            </div>
            <div class="gl-tp-private__rail" tabindex="0">
              @foreach($privateGroups as $group)
                <a href="{{ route('public.groups.show', $group->slug) }}" class="gl-tp-private__card">
                  <span class="gl-tp-private__badge">1:1</span>
                  <strong>{{ $group->title }}</strong>
                  <span class="gl-tp-private__meta">
                    <i class="fas fa-clock"></i> {{ (int) $group->duration_minutes }} {{ $isRtl ? 'دقيقة' : 'min' }}
                  </span>
                  <span class="gl-tp-private__price">{{ $group->formattedPrice() }}</span>
                </a>
              @endforeach
              @foreach($oneToOneCourses as $course)
                <a href="{{ route('public.course.show', $course->id) }}" class="gl-tp-private__card">
                  <span class="gl-tp-private__badge">{{ $isRtl ? 'كورس فردي' : '1:1 course' }}</span>
                  <strong>{{ $course->title }}</strong>
                  <span class="gl-tp-private__meta">
                    <i class="fas fa-book-open"></i> {{ (int) ($course->lessons_count ?? 0) }} {{ $isRtl ? 'درس' : 'lessons' }}
                  </span>
                  @if(!empty($course->price))
                    <span class="gl-tp-private__price">{{ format_money($course->price, null, 0) }}</span>
                  @endif
                </a>
              @endforeach
            </div>
          </section>
        @endif

        @if(count($experiences) > 0 || $profile->experience)
          <article class="gl-tp-card">
            <h2>{{ __('public.experience') }}</h2>
            @if(count($experiences) > 0)
              <ul class="gl-tp-exp">
                @foreach($experiences as $item)
                  <li>{{ $item }}</li>
                @endforeach
              </ul>
            @else
              <p class="gl-tp-bio">{{ $profile->sanitizedText($profile->experience) }}</p>
            @endif
          </article>
        @endif

        @if(count($skillChips) > 0 || count($skillNotes) > 0)
          <article class="gl-tp-card">
            <h2>{{ __('public.skills') }}</h2>
            @if(count($skillChips) > 0)
              <div class="gl-tp-skills">
                @foreach($skillChips as $skill)
                  <span class="gl-tp-skill">{{ $skill }}</span>
                @endforeach
              </div>
            @endif
            @if(count($skillNotes) > 0)
              <ul class="gl-tp-exp {{ count($skillChips) > 0 ? 'gl-tp-exp--after-skills' : '' }}">
                @foreach($skillNotes as $note)
                  <li>{{ $note }}</li>
                @endforeach
              </ul>
            @endif
          </article>
        @endif

        @if($groupCourses->isNotEmpty())
          <article class="gl-tp-card">
            <h2>{{ $isRtl ? 'كورسات جماعية' : 'Group courses' }}</h2>
            <div class="gl-tp-private__rail">
              @foreach($groupCourses as $course)
                <a href="{{ route('public.course.show', $course->id) }}" class="gl-tp-private__card">
                  <strong>{{ $course->title }}</strong>
                  <span class="gl-tp-private__meta">
                    <i class="fas fa-users"></i> {{ (int) ($course->lessons_count ?? 0) }} {{ $isRtl ? 'درس' : 'lessons' }}
                  </span>
                </a>
              @endforeach
            </div>
          </article>
        @endif
      </div>

      <aside class="gl-tp-aside">
        <div class="gl-tp-card gl-tp-book-card">
          <h3>{{ __('public.instructor_availability_title') }}</h3>
          @if(!empty($weeklyCalendar))
            <div class="gl-tp-cal">
              @foreach($weeklyCalendar as $col)
                <div class="gl-tp-cal__day">
                  <span class="gl-tp-cal__label">{{ $col['label'] }}</span>
                  <div class="gl-tp-cal__times">
                    @foreach($col['times'] as $t)
                      <span class="gl-tp-cal__t">{{ $t }}</span>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <p class="gl-tp-bio">{{ $isRtl ? 'لم يُحدَّد جدول توافر بعد.' : 'No weekly availability published yet.' }}</p>
          @endif

          @unless($canBook)
            <p class="gl-tp-note">
              {{ $isRtl
                ? 'يمكنك مشاهدة الملف والجدول. حجز الموعد يتاح بعد الاشتراك في باقة.'
                : 'You can browse the profile and schedule. Booking unlocks after you subscribe to a package.' }}
            </p>
            <div class="gl-tp-actions">
              <a href="{{ $packagesUrl }}" class="sana-btn sana-btn--yellow">
                {{ $isRtl ? 'اشترك في باقة للحجز' : 'Subscribe to book' }}
              </a>
              @guest
                <a href="{{ route('login', ['redirect' => $instrPageUrl]) }}" class="sana-btn sana-btn--purple-outline">
                  {{ $isRtl ? 'تسجيل الدخول' : 'Log in' }}
                </a>
              @endguest
            </div>
          @else
            <p class="gl-tp-note is-ok">
              {{ $isRtl ? ('رصيدك المتاح: '.$unitsLeft.' حصة — اختر المواعيد المناسبة.') : ('Available credits: '.$unitsLeft.' — pick the times that work for you.') }}
            </p>
            @if($bookableSlots->isNotEmpty())
              @php
                $clockTz = \App\Support\AppTimezone::forUser($profile->user);
                $viewerTz = \App\Support\AppTimezone::forUser(auth()->user());
                $weeklyOpts = $bookableSlots
                    ->map(function ($slot) use ($clockTz, $viewerTz) {
                        $starts = is_array($slot) ? ($slot['starts_at'] ?? null) : ($slot->starts_at ?? null);
                        if (! $starts instanceof \Carbon\Carbon) {
                            return null;
                        }
                        $clock = $starts->copy()->timezone($clockTz);
                        $viewer = $starts->copy()->timezone($viewerTz);
                        return [
                            'day' => (int) $clock->dayOfWeekIso,
                            'time' => $clock->format('H:i'),
                            'label' => $viewer->locale(app()->getLocale())->translatedFormat('l — g:i A'),
                        ];
                    })
                    ->filter()
                    ->unique(fn ($r) => $r['day'].'|'.$r['time'])
                    ->values();
              @endphp
              <form method="POST" action="{{ route('student.one-to-one-sessions.book-instructor', $profile->user) }}" class="gl-tp-book" id="glTpBookForm">
                @csrf
                <div class="gl-tp-style">
                  <label class="gl-tp-choice">
                    <input type="radio" name="booking_style" value="monthly" checked>
                    <span>
                      <strong>{{ $isRtl ? 'تثبيت شهري (موصى به)' : 'Monthly lock (recommended)' }}</strong>
                      <small>{{ $isRtl ? 'اختر حتى 7 مواعيد أسبوعياً لمدة تصل إلى 8 أسابيع' : 'Pick up to 7 weekly times for up to 8 weeks' }}</small>
                    </span>
                  </label>
                  <label class="gl-tp-choice">
                    <input type="radio" name="booking_style" value="multi">
                    <span>
                      <strong>{{ $isRtl ? 'عدة مواعيد' : 'Multiple slots' }}</strong>
                      <small>{{ $isRtl ? 'اختر أكثر من حصة مرة واحدة' : 'Select several sessions at once' }}</small>
                    </span>
                  </label>
                  <label class="gl-tp-choice">
                    <input type="radio" name="booking_style" value="single">
                    <span>
                      <strong>{{ $isRtl ? 'حصة واحدة' : 'Single session' }}</strong>
                    </span>
                  </label>
                </div>

                <div id="glTpMonthly" class="gl-tp-fields">
                  <label class="gl-tp-field">{{ $isRtl ? 'الموعد الأسبوعي 1' : 'Weekly slot 1' }}
                    <select id="glTpW0" required>
                      <option value="">{{ $isRtl ? 'اختر…' : 'Choose…' }}</option>
                      @foreach($weeklyOpts as $opt)
                        <option value="{{ $opt['day'] }}|{{ $opt['time'] }}">{{ $opt['label'] }}</option>
                      @endforeach
                    </select>
                    <input type="hidden" name="weekly_slots[0][day_of_week]" id="glTpW0Day">
                    <input type="hidden" name="weekly_slots[0][time]" id="glTpW0Time">
                  </label>
                  <label class="gl-tp-field">{{ $isRtl ? 'الموعد الأسبوعي 2' : 'Weekly slot 2' }}
                    <select id="glTpW1">
                      <option value="">{{ $isRtl ? 'اختياري…' : 'Optional…' }}</option>
                      @foreach($weeklyOpts as $opt)
                        <option value="{{ $opt['day'] }}|{{ $opt['time'] }}">{{ $opt['label'] }}</option>
                      @endforeach
                    </select>
                    <input type="hidden" name="weekly_slots[1][day_of_week]" id="glTpW1Day">
                    <input type="hidden" name="weekly_slots[1][time]" id="glTpW1Time">
                  </label>
                  <label class="gl-tp-field">{{ $isRtl ? 'الموعد الأسبوعي 3' : 'Weekly slot 3' }}
                    <select id="glTpW2">
                      <option value="">{{ $isRtl ? 'اختياري…' : 'Optional…' }}</option>
                      @foreach($weeklyOpts as $opt)
                        <option value="{{ $opt['day'] }}|{{ $opt['time'] }}">{{ $opt['label'] }}</option>
                      @endforeach
                    </select>
                    <input type="hidden" name="weekly_slots[2][day_of_week]" id="glTpW2Day">
                    <input type="hidden" name="weekly_slots[2][time]" id="glTpW2Time">
                  </label>
                  <label class="gl-tp-field">{{ $isRtl ? 'الموعد الأسبوعي 4' : 'Weekly slot 4' }}
                    <select id="glTpW3">
                      <option value="">{{ $isRtl ? 'اختياري…' : 'Optional…' }}</option>
                      @foreach($weeklyOpts as $opt)
                        <option value="{{ $opt['day'] }}|{{ $opt['time'] }}">{{ $opt['label'] }}</option>
                      @endforeach
                    </select>
                    <input type="hidden" name="weekly_slots[3][day_of_week]" id="glTpW3Day">
                    <input type="hidden" name="weekly_slots[3][time]" id="glTpW3Time">
                  </label>
                  <label class="gl-tp-field">{{ $isRtl ? 'عدد الأسابيع' : 'Weeks' }}
                    <select name="weeks">
                      <option value="4" selected>4</option>
                      <option value="3">3</option>
                      <option value="2">2</option>
                      <option value="6">6</option>
                      <option value="8">8</option>
                    </select>
                  </label>
                  <button type="submit" class="sana-btn sana-btn--yellow">
                    {{ $isRtl ? 'تثبيت الجدول الشهري' : 'Lock monthly schedule' }}
                  </button>
                </div>

                <div id="glTpMulti" class="gl-tp-book-pane" hidden>
                  <div class="gl-tp-slots">
                    @foreach($bookableSlots as $slot)
                      @php
                        $starts = is_array($slot) ? ($slot['starts_at'] ?? null) : ($slot->starts_at ?? null);
                        $label = is_array($slot) ? ($slot['label'] ?? null) : ($slot->label ?? null);
                        if ($starts instanceof \Carbon\Carbon) {
                          $value = $starts->copy()->utc()->toIso8601String();
                          $label = $label ?: $starts->copy()->timezone($viewerTz)->locale(app()->getLocale())->translatedFormat('D j M — g:i A');
                        } else {
                          continue;
                        }
                      @endphp
                      <label class="gl-tp-slot">
                        <input type="checkbox" name="scheduled_ats[]" value="{{ $value }}">
                        <span>{{ $label }}</span>
                      </label>
                    @endforeach
                  </div>
                  <button type="submit" class="sana-btn sana-btn--yellow">
                    {{ $isRtl ? 'حجز المواعيد المحددة' : 'Book selected slots' }}
                  </button>
                </div>

                <div id="glTpSingle" class="gl-tp-book-pane" hidden>
                  <div class="gl-tp-slots">
                    @foreach($bookableSlots as $slot)
                      @php
                        $starts = is_array($slot) ? ($slot['starts_at'] ?? null) : ($slot->starts_at ?? null);
                        $label = is_array($slot) ? ($slot['label'] ?? null) : ($slot->label ?? null);
                        if ($starts instanceof \Carbon\Carbon) {
                          $value = $starts->copy()->utc()->toIso8601String();
                          $label = $label ?: $starts->copy()->timezone($viewerTz)->locale(app()->getLocale())->translatedFormat('D j M — g:i A');
                        } else {
                          continue;
                        }
                      @endphp
                      <button type="submit" name="scheduled_at" value="{{ $value }}" class="gl-tp-slot" formnovalidate>
                        <span>{{ $label }}</span>
                        <i class="fas fa-calendar-plus"></i>
                      </button>
                    @endforeach
                  </div>
                </div>
              </form>
              <script>
              (function () {
                var form = document.getElementById('glTpBookForm');
                if (!form) return;
                var monthly = document.getElementById('glTpMonthly');
                var multi = document.getElementById('glTpMulti');
                var single = document.getElementById('glTpSingle');
                var w0 = document.getElementById('glTpW0');
                var w0Day = document.getElementById('glTpW0Day');
                var w0Time = document.getElementById('glTpW0Time');
                var weeklyCombos = [0, 1, 2, 3].map(function (i) {
                  return {
                    sel: document.getElementById('glTpW' + i),
                    day: document.getElementById('glTpW' + i + 'Day'),
                    time: document.getElementById('glTpW' + i + 'Time')
                  };
                });
                function syncCombo(sel, dayEl, timeEl) {
                  if (!sel || !dayEl || !timeEl) return;
                  var v = sel.value || '';
                  var p = v.split('|');
                  dayEl.value = p[0] || '';
                  timeEl.value = p[1] || '';
                }
                function sync() {
                  var style = (form.querySelector('input[name="booking_style"]:checked') || {}).value || 'monthly';
                  monthly.hidden = style !== 'monthly';
                  multi.hidden = style !== 'multi';
                  single.hidden = style !== 'single';
                  if (w0) w0.required = style === 'monthly';
                }
                form.querySelectorAll('input[name="booking_style"]').forEach(function (el) {
                  el.addEventListener('change', sync);
                });
                weeklyCombos.forEach(function (row) {
                  if (row.sel) row.sel.addEventListener('change', function () { syncCombo(row.sel, row.day, row.time); });
                });
                form.addEventListener('submit', function () {
                  weeklyCombos.forEach(function (row) { syncCombo(row.sel, row.day, row.time); });
                });
                sync();
              })();
              </script>
            @else
              <p class="gl-tp-bio">{{ $isRtl ? 'لا توجد مواعيد مفتوحة خلال الأسابيع القادمة.' : 'No open slots in the coming weeks.' }}</p>
            @endif
          @endunless
        </div>

        <div class="gl-tp-card">
          <h3>{{ $isRtl ? 'روابط سريعة' : 'Quick links' }}</h3>
          <div class="gl-tp-actions">
            <a href="{{ route('public.instructors.index') }}" class="sana-btn sana-btn--purple-outline">{{ __('public.all_instructors_link') }}</a>
            <a href="{{ $packagesUrl }}" class="sana-btn sana-btn--purple">{{ $isRtl ? 'باقات الحصص الخاصة' : 'Private lesson packages' }}</a>
          </div>
        </div>
      </aside>
    </div>
  </div>
</main>

@if($hasIntroVideo)
  <div class="gl-tp-modal" id="glTpIntroModal" hidden>
    <div class="gl-tp-modal__backdrop" data-close-intro></div>
    <div class="gl-tp-modal__dialog" role="dialog" aria-modal="true" aria-label="{{ $isRtl ? 'فيديو تعريفي' : 'Intro video' }}">
      <button type="button" class="gl-tp-modal__close" data-close-intro aria-label="{{ $isRtl ? 'إغلاق' : 'Close' }}"><i class="fas fa-times"></i></button>
      <div class="gl-tp-video">
        @if($introEmbedUrl)
          <iframe src="{{ $introEmbedUrl }}" title="{{ $name }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
        @elseif($introDirectVideo)
          <video controls playsinline preload="metadata" poster="{{ $profile->photo_url }}">
            <source src="{{ $introDirectVideo }}">
          </video>
        @endif
      </div>
    </div>
  </div>
  <script>
  (function () {
    var openBtn = document.getElementById('glTpIntroOpen');
    var modal = document.getElementById('glTpIntroModal');
    if (!openBtn || !modal) return;
    function open() { modal.hidden = false; document.body.style.overflow = 'hidden'; }
    function close() {
      modal.hidden = true;
      document.body.style.overflow = '';
      var v = modal.querySelector('video');
      if (v) { try { v.pause(); } catch (e) {} }
    }
    openBtn.addEventListener('click', open);
    modal.querySelectorAll('[data-close-intro]').forEach(function (el) {
      el.addEventListener('click', close);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modal.hidden) close();
    });
  })();
  </script>
@endif

@include('partials.landing.footer')
</body>
</html>
