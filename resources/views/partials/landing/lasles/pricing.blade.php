@php
    $dbPackages = \App\Models\Package::query()
        ->active()
        ->orderBy('order')
        ->orderByDesc('is_popular')
        ->limit(5)
        ->get();

    $artFor = fn ($type) => match ($type) {
        'free' => 'plan-free.svg',
        'advanced' => 'plan-premium.svg',
        default => 'plan-standard.svg',
    };

    $plansMeta = [
        ['key' => 'free', 'art' => 'plan-free.svg', 'featured' => false, 'cta' => 'register'],
        ['key' => 'individual', 'art' => 'plan-standard.svg', 'featured' => false, 'cta' => 'register'],
        ['key' => 'advanced', 'art' => 'plan-premium.svg', 'featured' => true, 'cta' => 'register'],
        ['key' => 'school', 'art' => 'plan-standard.svg', 'featured' => false, 'cta' => 'contact'],
    ];
@endphp
<section class="lasles-pricing" id="pricing">
  <div class="lasles-container">
    <h2 class="lasles-section-title">{{ __('landing.home.packages_title') }}</h2>
    <p class="lasles-section-lead">{{ __('landing.home.packages_lead') }}</p>
    @if($dbPackages->isNotEmpty())
      <div class="lasles-plans lasles-plans--{{ min(4, $dbPackages->count()) }}">
        @foreach($dbPackages->take(4) as $package)
          @include('partials.landing.lasles.package-card', [
            'package' => $package,
            'art' => $artFor($package->package_type),
            'img' => $img,
          ])
        @endforeach
      </div>
      <p class="lasles-section-lead" style="margin-top:1.5rem;text-align:center">
        <a href="{{ route('public.pricing') }}" class="lasles-btn-outline">{{ __('landing.home.packages_all') }}</a>
      </p>
    @else
      <div class="lasles-plans lasles-plans--4">
        @foreach($plansMeta as $meta)
          @php $plan = __('landing.home.plans.'.$meta['key']); @endphp
          <article class="lasles-plan {{ $meta['featured'] ? 'is-featured' : '' }}">
            @if($meta['featured'])
              <span class="lasles-plan__badge">{{ __('public.pricing_package_popular') }}</span>
            @endif
            <h3 class="lasles-plan__name">{{ $plan['name'] ?? '' }}</h3>
            <p class="lasles-plan__audience">{{ $plan['audience'] ?? '' }}</p>
            <p class="lasles-plan__price">
              <b>{{ $plan['price_html'] ?? ($plan['price'] ?? '') }}</b>
            </p>
            <div class="lasles-plan__cta-wrap">
              @if(($meta['cta'] ?? '') === 'contact')
                <a href="{{ route('public.contact') }}" class="lasles-btn-outline">{{ __('landing.home.cta_contact') }}</a>
              @else
                <a href="{{ route('register') }}" class="{{ $meta['featured'] ? 'lasles-btn-primary' : 'lasles-btn-outline' }}">
                  {{ __('landing.home.cta_start_journey') }}
                </a>
              @endif
            </div>
            <ul class="lasles-plan__list">
              @foreach(array_slice($plan['features'] ?? [], 0, 5) as $f)
                <li><img src="{{ $img('check-list.svg') }}" width="20" height="20" alt="">{{ $f }}</li>
              @endforeach
            </ul>
          </article>
        @endforeach
      </div>
    @endif
  </div>
</section>
