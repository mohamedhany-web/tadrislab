@extends('layouts.instructor-timeline')

@section('title', 'تعاقدات الجهات')
@section('page_title', 'تعاقدات الجهات المسندة')

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

<section class="st-join-hero">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">TADRIS LAB</p>
        <h2 class="st-join-hero__title">{{ $isRtl ? 'تنفيذ تعاقدات الجهات' : 'Institution delivery' }}</h2>
        <p class="st-join-hero__meta">{{ $isRtl ? 'برامج مسندة إليك — خاصة التعاقد المباشر.' : 'Programs assigned to you — especially direct contracts.' }}</p>
    </div>
</section>

@if($programs->isEmpty())
    <section class="st-panel st-lp-empty">
        <h3>{{ $isRtl ? 'لا برامج مسندة' : 'No assigned programs' }}</h3>
        <p>{{ $isRtl ? 'عندما تعيّنك الإدارة كمنفّذ لجهة ستظهر هنا.' : 'When admin assigns you as delivery coach, programs appear here.' }}</p>
    </section>
@else
    <section class="st-inst-list">
        @foreach($programs as $program)
            <a href="{{ route('instructor.institution-delivery.show', $program) }}" class="st-session-row st-inst-row">
                <div class="st-session-row__body">
                    <h3>{{ $program->title() }}</h3>
                    <p>
                        {{ $program->institution?->name() ?? '—' }}
                        · {{ $program->engagementModeLabel() }}
                        · {{ $program->statusLabel() }}
                    </p>
                </div>
                <span class="st-session-badge">{{ (int) $program->progress_percent }}%</span>
            </a>
        @endforeach
    </section>
    <div style="margin-top:1rem">{{ $programs->links() }}</div>
@endif
@endsection
