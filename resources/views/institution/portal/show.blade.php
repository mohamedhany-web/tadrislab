@extends('layouts.student-timeline')

@section('title', $institution->name())

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $programs = $institution->programs ?? collect();
    $members = $institution->members ?? collect();
    $programsCount = $programs->count();
    $membersCount = $members->count();
    $activePrograms = $programs->filter(fn ($p) => ! in_array($p->status, [
        \App\Models\InstitutionProgram::STATUS_CANCELLED,
        \App\Models\InstitutionProgram::STATUS_COMPLETED,
    ], true))->count();
    $avgProgress = $programsCount > 0
        ? (int) round($programs->avg(fn ($p) => (int) ($p->progress_percent ?? 0)))
        : 0;
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $institution->name(),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => $isRtl ? 'جهاتي' : 'My institutions', 'url' => route('institution.portal.index')],
        ['label' => $institution->name(), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="st-flash st-flash--err">{{ $errors->first() }}</div>
@endif

<section class="st-join-hero" aria-label="{{ $institution->name() }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">{{ $institution->orgTypeLabel() }}</p>
        <h2 class="st-join-hero__title">{{ $institution->name() }}</h2>
        <p class="st-join-hero__meta">
            {{ $isRtl ? 'عضويتك' : 'Your role' }}: {{ $member->roleLabel() }}
            @if($isCoordinator)
                · {{ $isRtl ? 'يمكنك إدارة المشاركين والبرامج' : 'You can manage participants & programs' }}
            @endif
        </p>
    </div>
    <div class="st-join-hero__actions">
        <a href="{{ route('dashboard') }}" class="st-pill st-pill--outline">{{ $isRtl ? 'لوحة التحكم' : 'Dashboard' }}</a>
        @if(Route::has('public.institutions.inquiry'))
            <a href="{{ route('public.institutions.inquiry') }}" class="st-pill st-pill--solid">{{ $isRtl ? 'استفسار جديد' : 'New inquiry' }}</a>
        @endif
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ $isRtl ? 'ملخص الجهة' : 'Institution summary' }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ $isRtl ? 'البرامج' : 'Programs' }}</p>
        <p class="st-stat-card__value">{{ $programsCount }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'نشط منها' : 'Active' }}: {{ $activePrograms }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ $isRtl ? 'الأعضاء' : 'Members' }}</p>
        <p class="st-stat-card__value">{{ $membersCount }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ $isRtl ? 'متوسط التقدّم' : 'Avg. progress' }}</p>
        <p class="st-stat-card__value">{{ $avgProgress }}%</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ $isRtl ? 'دورك' : 'Your role' }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ $member->roleLabel() }}</p>
    </article>
</section>

<section class="st-msg-intro">
    <div>
        <h2>{{ $isRtl ? 'البرامج' : 'Programs' }}</h2>
        <p>{{ $isRtl ? 'برامج التدريب والتطوير المرتبطة بهذه الجهة.' : 'Training and development programs for this institution.' }}</p>
    </div>
</section>

@if($programs->isEmpty())
    <section class="st-panel st-inst-empty">
        <p>{{ $isRtl ? 'لا برامج ظاهرة بعد — تُدار من لوحة الإدارة أو عبر استفسار مؤسسة.' : 'No programs yet — managed from admin or via an institution inquiry.' }}</p>
        @if(Route::has('public.institutions.inquiry'))
            <a href="{{ route('public.institutions.inquiry') }}" class="st-pill st-pill--solid">{{ $isRtl ? 'إرسال استفسار' : 'Send inquiry' }}</a>
        @endif
    </section>
@else
    <section class="st-inst-list" aria-label="{{ $isRtl ? 'البرامج' : 'Programs' }}">
        @foreach($programs as $program)
            <a href="{{ route('institution.portal.program', [$institution, $program]) }}" class="st-session-row st-inst-row">
                <div class="st-session-row__body">
                    <h3>{{ $program->title_ar ?? $program->title() ?? $program->name ?? ('برنامج #'.$program->id) }}</h3>
                    <p>
                        {{ $program->kindLabel() }}
                        · {{ $program->statusLabel() }}
                    </p>
                </div>
                <div class="st-session-row__actions">
                    <span class="st-session-badge">{{ (int) ($program->progress_percent ?? 0) }}%</span>
                    <span class="st-pill st-pill--outline st-pill--sm">{{ $isRtl ? 'فتح' : 'Open' }}</span>
                </div>
            </a>
        @endforeach
    </section>
@endif

<section class="st-msg-intro" style="margin-top:1.5rem">
    <div>
        <h2>{{ $isRtl ? 'الأعضاء' : 'Members' }}</h2>
        <p>{{ $isRtl ? 'المنسقون والمشاركون المرتبطون بالجهة.' : 'Coordinators and participants linked to this institution.' }}</p>
    </div>
</section>

<section class="st-panel">
    <ul class="st-inst-members">
        @foreach($members as $m)
            <li>
                <div>
                    <strong>{{ $m->displayName() }}</strong>
                    <span>{{ $m->email }}</span>
                </div>
                <span class="st-session-badge">{{ $m->roleLabel() }}</span>
            </li>
        @endforeach
    </ul>
</section>

@if($isCoordinator)
<section class="st-panel st-inst-form-panel" style="margin-top:1.25rem">
    <div class="st-section-head">
        <h2>{{ $isRtl ? 'إضافة مشارك' : 'Add participant' }}</h2>
        <p>{{ $isRtl ? 'أضف معلمًا مشاركًا تحت حساب الجهة.' : 'Add a teacher participant under this institution.' }}</p>
    </div>
    <form method="POST" action="{{ route('institution.portal.participants.store', $institution) }}" class="st-profile-form">
        @csrf
        <div class="st-profile-grid">
            <label class="st-field">
                <span>{{ $isRtl ? 'الاسم' : 'Name' }} *</span>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="{{ $isRtl ? 'اسم المشارك' : 'Participant name' }}">
            </label>
            <label class="st-field">
                <span>{{ $isRtl ? 'البريد' : 'Email' }} *</span>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="name@school.com">
            </label>
            <label class="st-field">
                <span>{{ $isRtl ? 'هاتف' : 'Phone' }}</span>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ $isRtl ? 'اختياري' : 'Optional' }}">
            </label>
            <label class="st-field">
                <span>{{ $isRtl ? 'المسمى' : 'Title' }}</span>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="{{ $isRtl ? 'معلم / منسق…' : 'Teacher / coordinator…' }}">
            </label>
        </div>
        <div class="st-profile-foot">
            <button type="submit" class="st-pill st-pill--solid">{{ $isRtl ? 'حفظ المشارك' : 'Save participant' }}</button>
        </div>
    </form>
</section>
@endif
@endsection
