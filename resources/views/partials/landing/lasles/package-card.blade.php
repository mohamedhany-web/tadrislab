@php
  /** @var \App\Models\Package $package */
  $img = $img ?? (fn (string $file) => lasles_img($file));
  $featured = $package->is_popular || $package->is_featured;
  $features = array_values(array_filter(is_array($package->features) ? $package->features : []));
  $tools = array_values(array_filter(is_array($package->tools_resources) ? $package->tools_resources : []));
  $allBenefits = array_values(array_unique(array_merge($features, $tools)));
  $previewBenefits = array_slice($allBenefits, 0, 5);
  $extraBenefits = array_slice($allBenefits, 5);
  $typeLabel = $package->typeLabel();
  $audience = filled($package->card_summary)
      ? $package->card_summary
      : (
          \Illuminate\Support\Facades\Lang::has('landing.home.package_audience.'.$package->package_type)
              ? __('landing.home.package_audience.'.$package->package_type)
              : $typeLabel
      );
  $ctaClass = ($featured && ! $package->isQuoteOnly()) ? 'lasles-btn-primary' : 'lasles-btn-outline';
  $ctaText = $package->isQuoteOnly()
      ? __('landing.home.cta_contact')
      : __('landing.home.cta_start_journey');
  $detailsId = 'plan-details-'.$package->id;
@endphp
<article class="lasles-plan {{ $featured ? 'is-featured' : '' }} lasles-plan--type-{{ $package->package_type ?: 'individual' }}">
  @if($featured)
    <span class="lasles-plan__badge">{{ __('public.pricing_package_popular') }}</span>
  @endif

  @if($typeLabel !== $package->name)
    <p class="lasles-plan__type">{{ $typeLabel }}</p>
  @endif
  <h3 class="lasles-plan__name">{{ $package->name }}</h3>
  <p class="lasles-plan__audience">{{ $audience }}</p>

  <div class="lasles-plan__pricing">
    <p class="lasles-plan__price">
      <b>{{ $package->formattedPrice() }}</b>
      @if($package->formattedOriginalPrice())
        <span class="lasles-plan__old">{{ $package->formattedOriginalPrice() }}</span>
      @endif
    </p>
    @if($package->durationLabel())
      <p class="lasles-plan__duration">{{ $package->durationLabel() }}</p>
    @endif
  </div>

  <div class="lasles-plan__specs">
    @if($package->consultation_sessions)
      <span>{{ __('public.pricing_meta_consult', ['count' => $package->consultation_sessions]) }}</span>
    @endif
    @if($package->participant_seats && $package->participant_seats > 1)
      <span>{{ __('public.pricing_meta_seats', ['count' => $package->participant_seats]) }}</span>
    @endif
    @if($package->includes_tools)
      <span>{{ __('public.pricing_meta_tools') }}</span>
    @endif
  </div>

  <div class="lasles-plan__cta-wrap">
    <a href="{{ $package->ctaUrl() }}" class="{{ $ctaClass }}">{{ $ctaText }}</a>
  </div>

  @if(count($previewBenefits))
    <ul class="lasles-plan__list">
      @foreach($previewBenefits as $feature)
        <li><span class="lasles-plan__check" aria-hidden="true"></span>{{ $feature }}</li>
      @endforeach
    </ul>
  @endif

  @if(count($extraBenefits) || filled($package->discount_note))
    <button
      type="button"
      class="lasles-plan__more"
      data-plan-more
      data-show-label="{{ __('landing.home.packages_show_all') }}"
      data-hide-label="{{ __('landing.home.packages_hide_all') }}"
      aria-expanded="false"
      aria-controls="{{ $detailsId }}"
    >
      {{ __('landing.home.packages_show_all') }}
    </button>
    <div class="lasles-plan__details" id="{{ $detailsId }}" hidden>
      @if(count($extraBenefits))
        <ul class="lasles-plan__list">
          @foreach($extraBenefits as $feature)
            <li><span class="lasles-plan__check" aria-hidden="true"></span>{{ $feature }}</li>
          @endforeach
        </ul>
      @endif
      @if(filled($package->discount_note))
        <p class="lasles-plan__note">{{ $package->discount_note }}</p>
      @endif
    </div>
  @endif
</article>
