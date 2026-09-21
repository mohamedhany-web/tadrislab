@extends('layouts.public')

@php
    $brand = config('app.name');
@endphp

@section('title', __('public.privacy_page_title') . ' - ' . __('public.site_suffix'))
@section('meta_description', __('public.legal_privacy_meta', ['brand' => $brand]))
@section('meta_keywords', __('public.legal_privacy_keywords', ['brand' => $brand]))
@section('canonical_url', url('/privacy'))

@push('styles')
<style>
    .privacy-home-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border: 1px solid rgb(226 232 240);
    }
    .privacy-home-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 44px -22px rgba(31, 42, 122, 0.28);
    }
    html.dark .privacy-home-card {
        border-color: rgb(51 65 85);
        background: rgb(30 41 59 / 0.92);
    }
</style>
@endpush

@section('content')
{{-- هيرو بنفس أسلوب الصفحة الرئيسية (welcome) --}}
<section class="pt-24 sm:pt-28 lg:pt-32 pb-12 sm:pb-14 overflow-hidden relative" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
    <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-6" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
                <i class="fas fa-shield-halved"></i> {{ __('public.privacy_page_title') }}
            </span>
            <h1 class="text-[1.85rem] sm:text-[2.5rem] lg:text-[3.1rem] leading-[1.2] font-black mb-4 text-[#1F2A7A] dark:text-white" style="font-family:Tajawal,Cairo,sans-serif">
                {{ __('public.privacy_short') }}
                <span class="block mt-1 text-[#FB5607] dark:text-orange-400">{{ $brand }}</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg leading-8 mb-8 max-w-3xl mx-auto">
                {{ __('public.legal_privacy_hero_sub') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                <a href="{{ route('public.contact') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold text-white px-7 py-3.5 shadow-lg transition-all hover:scale-[1.02]" style="background:#FB5607;box-shadow:0 12px 28px -10px rgba(251,86,7,.45)">
                    <i class="fas fa-envelope"></i> {{ __('public.contact_page_title') }}
                </a>
                <a href="{{ route('public.terms') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold px-7 py-3.5 border-2 transition-all hover:opacity-95 text-white" style="background:#283593;border-color:#283593">
                    <i class="fas fa-file-contract"></i> {{ __('public.terms_page_title') }}
                </a>
            </div>
        </div>
    </div>
</section>

{{-- مقدمة — كارت مثل CTA الرئيسية --}}
<section class="py-10 sm:py-12 bg-white dark:bg-slate-800 border-t border-slate-100 dark:border-slate-800">
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8">
        <div class="rounded-[28px] border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-[0_20px_44px_-26px_rgba(31,42,122,.28)] px-6 sm:px-10 py-8 sm:py-10">
            <div class="flex flex-col sm:flex-row sm:items-start gap-6">
                <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 rounded-2xl flex items-center justify-center text-white text-xl shadow-md" style="background:linear-gradient(135deg,#283593,#1F2A7A)">
                    <i class="fas fa-lock"></i>
                </div>
                <p class="text-slate-700 dark:text-slate-200 text-base md:text-lg leading-[1.9] flex-1">
                    {!! nl2br(e(__('public.legal_privacy_intro', ['brand' => $brand]))) !!}
                </p>
            </div>
        </div>
    </div>
</section>

{{-- البنود — شبكة كروت مثل كروت الإحصائيات في الرئيسية --}}
<section class="py-12 sm:py-16 bg-gradient-to-b from-slate-50 to-white dark:from-slate-900 dark:to-slate-800">
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 lg:gap-6">
            @php
                $privacyIcons = ['database', 'file-lines', 'lock', 'share-nodes', 'user-check', 'cookie-bite', 'arrows-rotate'];
            @endphp
            @foreach(range(1, 7) as $i)
            @php
                $tintRose = in_array($i, [2, 5], true);
                $tintCream = $i === 4;
            @endphp
            <article class="privacy-home-card rounded-2xl p-6 sm:p-7 flex flex-col h-full @if($tintRose) bg-[#FFE5F7]/90 dark:bg-slate-800 @elseif($tintCream) bg-[#fffbea] dark:bg-slate-800 @else bg-white dark:bg-slate-800 @endif @if($i === 7) md:col-span-2 @endif">
                <div class="flex items-start gap-4 mb-3">
                    <span class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center text-white text-base shrink-0 shadow-[0_8px_20px_-8px_rgba(31,42,122,.35)]" style="background:{{ $i % 2 === 0 ? '#FB5607' : '#283593' }}">
                        <i class="fas fa-{{ $privacyIcons[$i-1] }}"></i>
                    </span>
                    <h2 class="text-lg sm:text-xl font-black leading-snug pt-1 flex-1 text-[#1F2A7A] dark:text-white" style="font-family:Tajawal,Cairo,sans-serif">
                        {{ __('public.legal_privacy_s'.$i.'_title') }}
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm sm:text-base flex-1 @if($i === 7) max-w-4xl @endif">
                    {!! nl2br(e(__('public.legal_privacy_s'.$i.'_body', ['brand' => $brand]))) !!}
                </p>
            </article>
            @endforeach
        </div>
    </div>
</section>

{{-- دعوة للتواصل — نفس قسم CTA في الرئيسية --}}
<section class="pt-14 sm:pt-16 pb-12 sm:pb-14" style="background:linear-gradient(180deg,#f4f7ff 0%,#ffffff 100%)">
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8">
        <div class="rounded-[28px] border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-[0_20px_44px_-26px_rgba(31,42,122,.28)] px-6 sm:px-10 py-10 sm:py-12 text-center">
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5" style="background:#FFE5F7;color:#283593">
                <i class="fas fa-headset"></i> {{ __('public.support') }}
            </span>
            <h3 class="text-2xl sm:text-3xl md:text-4xl font-black mb-3 text-[#1F2A7A] dark:text-white" style="font-family:Tajawal,Cairo,sans-serif">{{ __('public.legal_privacy_cta_title') }}</h3>
            <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg max-w-2xl mx-auto leading-8 mb-8">
                {{ __('public.legal_privacy_cta_desc') }}
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                <a href="{{ route('public.contact') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold text-white px-8 py-3.5 transition-all hover:scale-[1.02]" style="background:#FB5607;box-shadow:0 12px 28px -10px rgba(251,86,7,.45)">
                    <i class="fas fa-paper-plane"></i> {{ __('public.contact_page_title') }}
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl font-bold px-8 py-3.5 border-2 border-slate-200 dark:border-slate-600 text-[#1F2A7A] dark:text-slate-100 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <i class="fas fa-home"></i> {{ __('public.home') }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
