@extends('layouts.instructor-timeline')

@section('title', __('instructor.tc_community_title') . ' · ' . $cohort->title)
@section('page_title', __('instructor.tc_community_title'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $feedPosts = $feedPosts ?? collect();
    $canModerateFeed = $canModerateFeed ?? true;
@endphp

<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.tutoring-cohorts.show', $cohort) }}">{{ __('instructor.tc_back_command') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.tc_community_title') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-comments su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.tc_community_title') }}
            </h1>
            <p class="su-page-head__sub">{{ $cohort->title }} — {{ __('instructor.tc_community_sub') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.tutoring-cohorts.show', $cohort) }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.tc_back_command') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:14px 18px;background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.25);color:#15803d;font-size:13px;font-weight:600">
            <i class="fas fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
        </div>
    @endif

    <section class="su-card" style="margin-bottom:20px">
        <h2 class="su-card__title">
            <i class="fas fa-pen" aria-hidden="true"></i>
            {{ __('instructor.tc_publish_short') }}
        </h2>
        <form method="POST" action="{{ route('instructor.tutoring-cohorts.feed.store', $cohort) }}">
            @csrf
            <div class="su-field" style="margin-bottom:12px">
                <label for="community_body">{{ __('instructor.tc_write_ph') }}</label>
                <textarea name="body" id="community_body" rows="3" maxlength="1000" required
                          class="su-input" style="height:auto;min-height:96px;padding:10px 12px;resize:vertical"
                          placeholder="{{ __('instructor.tc_write_ph') }}"></textarea>
            </div>
            <div class="su-form-actions" style="flex-wrap:wrap">
                <div class="su-field" style="margin:0;min-width:160px">
                    <label for="post_type">{{ __('instructor.tc_type_announcement') }}</label>
                    <select name="post_type" id="post_type" class="su-select">
                        <option value="announcement">{{ __('instructor.tc_type_announcement') }}</option>
                        <option value="question">{{ __('instructor.tc_type_post') }}</option>
                    </select>
                </div>
                <label style="display:inline-flex;align-items:center;gap:8px;font-size:12px;font-weight:600;color:var(--su-ink-40)">
                    <input type="checkbox" name="is_pinned" value="1"> {{ __('instructor.tc_pin') }}
                </label>
                <button type="submit" class="su-btn su-btn--primary" style="height:40px;margin-inline-start:auto">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    {{ __('instructor.tc_publish_short') }}
                </button>
            </div>
        </form>
    </section>

    <div class="su-list">
        @forelse($feedPosts as $post)
            <article class="su-card" style="margin-bottom:12px;{{ $post->is_hidden ? 'background:rgba(239,68,68,.06);border-color:rgba(239,68,68,.25)' : '' }}">
                <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:10px">
                    <div class="min-w-0">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                            <strong style="font-size:14px;color:var(--su-ink)">{{ $post->author?->name }}</strong>
                            <span class="su-chip {{ $post->is_pinned ? 'su-chip--warn' : 'su-soft-1' }}">{{ $post->typeLabel() }}</span>
                            @if($post->is_pinned)
                                <span class="su-chip su-chip--warn"><i class="fas fa-thumbtack" aria-hidden="true"></i> {{ __('instructor.tc_pin') }}</span>
                            @endif
                            @if($post->is_hidden)
                                <span class="su-chip su-chip--off">{{ __('instructor.tc_hide') }}</span>
                            @endif
                        </div>
                        <p style="margin:4px 0 0;font-size:11px;color:var(--su-ink-40);font-weight:600">{{ $post->created_at?->diffForHumans() }}</p>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px">
                        <form method="POST" action="{{ route('instructor.class-feed.pin', $post) }}">
                            @csrf
                            <button type="submit" class="su-btn" style="height:32px">
                                {{ $post->is_pinned ? __('instructor.tc_unpin') : __('instructor.tc_pin') }}
                            </button>
                        </form>
                        @if($post->is_hidden)
                            <form method="POST" action="{{ route('instructor.class-feed.unhide', $post) }}">
                                @csrf
                                <button type="submit" class="su-btn" style="height:32px">{{ __('instructor.tc_unhide') }}</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('instructor.class-feed.hide', $post) }}">
                                @csrf
                                <button type="submit" class="su-btn" style="height:32px">{{ __('instructor.tc_hide') }}</button>
                            </form>
                        @endif
                    </div>
                </div>

                <p style="margin:14px 0 0;font-size:13px;line-height:1.55;color:var(--su-ink);white-space:pre-wrap">{{ $post->body }}</p>

                @if($post->visibleComments && $post->visibleComments->isNotEmpty())
                    <div class="su-list" style="margin-top:14px;padding-top:14px;border-top:0.5px solid var(--su-line)">
                        @foreach($post->visibleComments as $comment)
                            <div class="su-list-item" style="background:var(--su-bg)">
                                <span class="su-list-item__ico su-soft-3">
                                    <i class="fas fa-reply" aria-hidden="true"></i>
                                </span>
                                <div class="su-list-item__body">
                                    <div class="su-list-item__title">{{ $comment->author?->name }}</div>
                                    <div class="su-list-item__meta">{{ $comment->created_at?->diffForHumans() }}</div>
                                    <p style="margin:6px 0 0;font-size:13px;white-space:pre-wrap;color:var(--su-ink)">{{ $comment->body }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('instructor.class-feed.comment', $post) }}" class="su-form-actions" style="margin-top:14px;align-items:stretch">
                    @csrf
                    <input type="text" name="body" required maxlength="1000" class="su-input" style="flex:1;min-width:180px"
                           placeholder="{{ __('instructor.tc_comment_ph') }}">
                    <button type="submit" class="su-btn" style="height:40px">{{ __('instructor.tc_comment') }}</button>
                </form>
            </article>
        @empty
            <div class="su-empty">
                <i class="fas fa-comments" aria-hidden="true"></i>
                <p>{{ __('instructor.tc_no_community_posts') }}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
