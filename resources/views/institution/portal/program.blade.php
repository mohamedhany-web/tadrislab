@extends('layouts.student-timeline')

@section('title', $program->title() ?: ($program->title_ar ?? 'برنامج'))

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $programTitle = $program->title() ?: ($program->title_ar ?? ('برنامج #'.$program->id));
    $progress = (int) ($program->progress_percent ?? 0);
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $programTitle,
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => $institution->name(), 'url' => route('institution.portal.show', $institution)],
        ['label' => $programTitle, 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="st-flash st-flash--err">{{ $errors->first() }}</div>
@endif

<section class="st-join-hero" aria-label="{{ $programTitle }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">{{ $program->kindLabel() }} · {{ $program->engagementModeLabel() }}</p>
        <h2 class="st-join-hero__title">{{ $programTitle }}</h2>
        <p class="st-join-hero__meta">
            {{ $program->statusLabel() }} · {{ $isRtl ? 'التقدّم' : 'Progress' }}: {{ $progress }}%
            @if($program->instructor)
                · {{ $isRtl ? 'المدرب' : 'Coach' }}: {{ $program->instructor->name }}
            @endif
            @if($isPlatform && $seatCap !== null)
                · {{ $isRtl ? 'مقاعد' : 'Seats' }}: {{ $program->participants->count() }}/{{ $seatCap }}
                @if($seatsRemaining !== null) ({{ $isRtl ? 'متبقي' : 'left' }} {{ $seatsRemaining }}) @endif
            @endif
        </p>
    </div>
    <div class="st-join-hero__actions">
        <a href="{{ route('institution.portal.show', $institution) }}" class="st-pill st-pill--outline">{{ $isRtl ? 'رجوع للجهة' : 'Back to institution' }}</a>
    </div>
</section>

<section class="st-stats st-stats--classes">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ $isRtl ? 'الحالة' : 'Status' }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ $program->statusLabel() }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ $isRtl ? 'التقدّم' : 'Progress' }}</p>
        <p class="st-stat-card__value">{{ $progress }}%</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ $isRtl ? 'المشاركون' : 'Participants' }}</p>
        <p class="st-stat-card__value">{{ $program->participants->count() }}</p>
    </article>
</section>

@if($program->proposal_notes || $program->price)
<section class="st-panel st-inst-proposal">
    <div class="st-section-head">
        <h2>{{ $isRtl ? 'عرض TADRIS LAB' : 'TADRIS LAB proposal' }}</h2>
        <p>{{ $isRtl ? 'راجع العرض ثم اقبل أو ارفض كمنسّق.' : 'Review the proposal, then accept or reject as coordinator.' }}</p>
    </div>
    @if($program->price)
        <p class="st-inst-proposal__price">
            {{ $isRtl ? 'السعر المقترح:' : 'Proposed price:' }}
            <strong>{{ number_format((float) $program->price, 2) }} {{ $program->currency ?: 'QAR' }}</strong>
        </p>
    @endif
    @if($program->proposal_notes)
        <p class="st-inst-proposal__notes">{{ $program->proposal_notes }}</p>
    @endif

    @if($isCoordinator && $program->canCoordinatorAccept())
        <div class="st-inst-proposal__actions">
            <form method="POST" action="{{ route('institution.portal.program.accept', [$institution, $program]) }}">
                @csrf
                <button type="submit" class="st-pill st-pill--solid">{{ $isRtl ? 'قبول العرض' : 'Accept proposal' }}</button>
            </form>
            <form method="POST" action="{{ route('institution.portal.program.reject', [$institution, $program]) }}" onsubmit="return confirm('{{ $isRtl ? 'تأكيد رفض العرض؟' : 'Reject this proposal?' }}')">
                @csrf
                <button type="submit" class="st-pill st-pill--outline st-pill--danger">{{ $isRtl ? 'رفض' : 'Reject' }}</button>
            </form>
        </div>
    @endif
</section>
@elseif($isCoordinator && $program->canCoordinatorReject())
    <form method="POST" action="{{ route('institution.portal.program.reject', [$institution, $program]) }}" class="st-inst-proposal__actions" style="margin-bottom:1rem" onsubmit="return confirm('{{ $isRtl ? 'إلغاء هذا الطلب؟' : 'Cancel this request?' }}')">
        @csrf
        <button type="submit" class="st-pill st-pill--outline st-pill--danger">{{ $isRtl ? 'إلغاء الطلب' : 'Cancel request' }}</button>
    </form>
@endif

<section class="st-msg-intro">
    <div>
        <h2>{{ $isDirect ? ($isRtl ? 'تنفيذ عبر المدرب' : 'Coach delivery') : ($isRtl ? 'المشاركون في البرنامج' : 'Program participants') }}</h2>
        <p>
            @if($isDirect)
                {{ $isRtl ? 'تعاقد مباشر: المدرب المعيَّن ينفّذ الخدمة ويحدّث التقدّم — لا حاجة لمقاعد منصة.' : 'Direct contract: the assigned coach delivers and updates progress — no platform seats required.' }}
            @else
                {{ $isRtl ? 'تعاقد منصة: فعّل المشاركين ضمن المقاعد وتابع تقدّمهم.' : 'Platform contract: activate participants within seats and track progress.' }}
            @endif
        </p>
    </div>
</section>

@if($isDirect)
    <section class="st-panel">
        <p><strong>{{ $isRtl ? 'المدرب المنفّذ:' : 'Delivery coach:' }}</strong> {{ $program->instructor?->name ?? ($isRtl ? 'لم يُسند بعد — تواصل مع الإدارة' : 'Not assigned yet — contact admin') }}</p>
        @if($program->result_notes)
            <p style="margin-top:.75rem">{{ $program->result_notes }}</p>
        @endif
        @if($program->progress_percent)
            <p style="margin-top:.5rem">{{ $isRtl ? 'تقدّم التنفيذ' : 'Delivery progress' }}: {{ (int) $program->progress_percent }}%</p>
        @endif
    </section>
@elseif($program->participants->isEmpty())
    <section class="st-panel st-inst-empty">
        <p>{{ $isRtl ? 'لا مشاركين مفعّلين بعد.' : 'No participants activated yet.' }}</p>
    </section>
@else
    <section class="st-inst-list">
        @foreach($program->participants as $p)
            <article class="st-session-row st-inst-row">
                <div class="st-session-row__body">
                    <h3>{{ $p->name ?? $p->user?->name ?? '—' }}</h3>
                    <p>{{ $p->statusLabel() }} · {{ (int) ($p->progress_percent ?? 0) }}%</p>
                </div>
                @if($isCoordinator && ($program->isDeliverable() || $program->status === \App\Models\InstitutionProgram::STATUS_APPROVED))
                    <form method="POST" action="{{ route('institution.portal.program.participant.progress', [$institution, $program, $p]) }}" class="st-inst-progress-form">
                        @csrf
                        <input type="number" name="progress_percent" min="0" max="100" value="{{ (int) $p->progress_percent }}" aria-label="{{ $isRtl ? 'التقدّم' : 'Progress' }}">
                        <select name="status" aria-label="{{ $isRtl ? 'الحالة' : 'Status' }}">
                            @foreach(\App\Models\InstitutionProgramParticipant::STATUSES as $key => $label)
                                <option value="{{ $key }}" @selected($p->status === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="st-pill st-pill--solid st-pill--sm">{{ $isRtl ? 'حفظ' : 'Save' }}</button>
                    </form>
                @else
                    <span class="st-session-badge">{{ (int) ($p->progress_percent ?? 0) }}%</span>
                @endif
            </article>
        @endforeach
    </section>
@endif

@if($isPlatform && $isCoordinator && ($program->isDeliverable() || $program->status === \App\Models\InstitutionProgram::STATUS_APPROVED))
<section class="st-panel st-inst-form-panel" style="margin-top:1.25rem">
    <div class="st-section-head">
        <h2>{{ $isRtl ? 'تفعيل مشارك على مقعد' : 'Activate participant seat' }}</h2>
        <p>
            @if($seatsRemaining === null)
                {{ $isRtl ? 'اختر عضوًا من الجهة وفعّله في هذا البرنامج.' : 'Pick an org member and activate them on this program.' }}
            @elseif($seatsRemaining > 0)
                {{ $isRtl ? 'متبقي' : 'Remaining' }}: {{ $seatsRemaining }} {{ $isRtl ? 'مقعد' : 'seats' }}
            @else
                {{ $isRtl ? 'لا مقاعد متبقية.' : 'No seats left.' }}
            @endif
        </p>
    </div>
    @if($seatsRemaining === null || $seatsRemaining > 0)
    <form method="POST" action="{{ route('institution.portal.program.enroll', [$institution, $program]) }}" class="st-inst-enroll-form">
        @csrf
        <label class="st-field st-field--full">
            <span>{{ $isRtl ? 'العضو' : 'Member' }}</span>
            <select name="institution_member_id" required>
                <option value="">{{ $isRtl ? 'اختر عضوًا…' : 'Choose a member…' }}</option>
                @foreach($orgMembers as $om)
                    <option value="{{ $om->id }}">{{ $om->displayName() }} — {{ $om->email }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit" class="st-pill st-pill--solid">{{ $isRtl ? 'تفعيل المقعد' : 'Activate seat' }}</button>
    </form>
    @endif
</section>
@endif
@endsection
