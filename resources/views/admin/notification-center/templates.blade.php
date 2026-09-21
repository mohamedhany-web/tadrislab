@extends('layouts.admin')

@section('title', 'قوالب الإشعارات')
@section('page_title', 'قوالب الإشعارات')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-muted"><a href="{{ route('admin.notification-center.index') }}" class="hover:text-accent">مركز الإشعارات</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">قوالب WhatsApp / Email</h2>
            <p class="mt-1 text-sm text-muted">placeholders مثل <code>:name</code> <code>:order_id</code> <code>:when</code> تُستبدل تلقائياً.</p>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.notification-center.templates.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <article class="grid gap-4 rounded-2xl border border-line bg-surface p-5 shadow-soft lg:grid-cols-2">
            <label class="flex items-center gap-3 text-sm text-ink">
                <input type="hidden" name="enabled" value="0">
                <input type="checkbox" name="enabled" value="1" class="rounded border-line" @checked($enabled)>
                تفعيل طبقة الإشعارات المركزية
            </label>
            <div>
                <p class="mb-2 text-xs font-medium text-muted">القنوات النشطة</p>
                <div class="flex flex-wrap gap-3">
                    @foreach(array_merge($mvpChannels, $laterChannels) as $ch)
                        <label class="inline-flex items-center gap-2 text-sm text-ink">
                            <input type="checkbox" name="channels[]" value="{{ $ch }}" class="rounded border-line"
                                   @checked(in_array($ch, $channels, true))>
                            {{ strtoupper($ch) }}
                            @if(in_array($ch, $laterChannels, true))
                                <span class="text-[10px] text-muted">لاحقاً</span>
                            @endif
                        </label>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-muted">SMS / Push جاهزة معمارياً ويمكن تفعيلها من هنا عند الجاهزية.</p>
            </div>
        </article>

        @foreach($templates as $key => $tpl)
            <article class="space-y-3 rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-sm font-semibold text-ink">{{ $eventLabels[$key] ?? $key }}</h3>
                    @if(!empty($tpl['overridden']))
                        <span class="rounded-full bg-accent/10 px-2 py-0.5 text-[11px] text-accent">مخصّص</span>
                    @endif
                </div>
                <div>
                    <label class="mb-1.5 block text-xs text-muted">الموضوع (Email)</label>
                    <input class="{{ $fieldClass }}" type="text" name="events[{{ $key }}][subject]" value="{{ old('events.'.$key.'.subject', $tpl['subject']) }}">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs text-muted">النص (WhatsApp + Email)</label>
                    <textarea class="{{ $areaClass }}" name="events[{{ $key }}][body]" rows="5">{{ old('events.'.$key.'.body', $tpl['body']) }}</textarea>
                </div>
            </article>
        @endforeach

        <button type="submit" class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-6 text-sm font-medium text-white">حفظ التغييرات</button>
    </form>
</div>
@endsection
