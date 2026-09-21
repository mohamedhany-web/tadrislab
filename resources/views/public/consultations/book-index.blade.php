@extends('layouts.lasles-public')

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

<section class="lasles-paths-hero lasles-paths-hero--detail">
  <div class="lasles-container lasles-paths-hero__inner">
    <p class="lasles-paths-hero__brand">{{ 'TADRIS LAB' }}</p>
    <h1 class="lasles-paths-hero__title">{{ $isRtl ? 'الاستشارات' : 'Consultations' }}</h1>
    <p class="lasles-paths-hero__lead">
      {{ $isRtl
        ? 'قسم مستقل عن المسارات: اختر نوع الاستشارة، اطّلع على المدة والسعر، احجز موعدًا، وأكمل الدفع.'
        : 'Independent of learning paths: choose a type, review duration & price, pick a slot, then pay.' }}
    </p>
  </div>
</section>

<section class="lasles-container" style="padding:2rem 0 4rem">
  @forelse($types as $typeKey => $typeMeta)
    @php
      $label = is_array($typeMeta) ? ($isRtl ? ($typeMeta['ar'] ?? $typeKey) : ($typeMeta['en'] ?? $typeMeta['ar'] ?? $typeKey)) : $typeMeta;
      $list = $servicesByType->get($typeKey, collect());
    @endphp
    <div style="margin-bottom:2.5rem">
      <h2 class="lasles-section-title" style="font-size:1.35rem">{{ $label }}</h2>
      @if($list->isEmpty())
        <p class="lasles-section-lead">{{ $isRtl ? 'لا خدمات منشورة لهذا النوع حاليًا.' : 'No published services for this type yet.' }}</p>
      @else
        <div style="display:grid;gap:1rem;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));margin-top:1rem">
          @foreach($list as $service)
            <a href="{{ route('public.consultations.book.service', $service->slug) }}" style="display:block;padding:1.25rem;border:1px solid #ddd;border-radius:12px;text-decoration:none;color:inherit">
              <h3 style="font-size:1.05rem;font-weight:700;margin:0 0 .5rem">{{ $service->title() }}</h3>
              @if($service->summary())
                <p style="font-size:.9rem;opacity:.8;margin:0 0 .75rem">{{ $service->summary() }}</p>
              @endif
              <p style="font-size:.95rem;font-weight:600;margin:0">
                {{ number_format((float) $service->price, 0) }} {{ $service->currency }}
                · {{ (int) $service->duration_minutes }} {{ $isRtl ? 'دقيقة' : 'min' }}
              </p>
            </a>
          @endforeach
        </div>
      @endif
    </div>
  @empty
    <p class="lasles-section-lead">{{ $isRtl ? 'خدمات الاستشارات قيد الإعداد من لوحة الإدارة.' : 'Consultation services are being set up in admin.' }}</p>
  @endforelse
</section>
@endsection
