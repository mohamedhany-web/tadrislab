@extends('layouts.lasles-public')

@section('content')
<section class="lasles-section">
  <div class="lasles-container" style="max-width:800px">
    <p class="lasles-eyebrow"><a href="{{ route('public.pricing') }}">الباقات</a></p>
    <h1 class="lasles-section-title">{{ $package->name }}</h1>
    <p class="lasles-section-lead">{{ $package->description ?: 'باقة تطوير مهني للمعلمين' }}</p>
    <p style="font-size:1.4rem;font-weight:700;margin:1rem 0">{{ $package->formattedPrice(2) }}</p>

    @if($package->learningPaths->isNotEmpty())
      <h2 class="lasles-section-title" style="font-size:1.15rem">المسارات المشمولة</h2>
      <ul>
        @foreach($package->learningPaths as $path)
          <li><a href="{{ route('public.learning-paths.show', $path->slug) }}">{{ $path->title() }}</a></li>
        @endforeach
      </ul>
    @endif

    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-top:1.5rem">
      @if($package->isQuoteOnly())
        <a href="{{ route('public.contact', ['topic' => 'package', 'package' => $package->slug]) }}" class="lasles-btn-primary">طلب عرض</a>
      @else
        <a href="{{ route('public.packages.checkout', $package->slug) }}" class="lasles-btn-primary">اشترِ الباقة</a>
      @endif
      <a href="{{ route('public.contact', ['topic' => 'package']) }}" class="lasles-btn-outline">استفسار</a>
    </div>
  </div>
</section>
@endsection
