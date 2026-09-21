@php
    use App\Support\TadrisPublicNav;
    $services = TadrisPublicNav::services();
    $pillarCopy = __('landing.home.pillars');
    $icons = [
        'learning_paths' => 'icon-paths.svg',
        'tools_resources' => 'icon-tools.svg',
        'consultations' => 'check-list.svg',
        'packages' => 'check-green.svg',
        'schools_institutions' => 'icon-teachers.svg',
    ];
@endphp
<section class="lasles-pillars" id="services" aria-labelledby="pillars-title">
  <div class="lasles-container">
    <div class="lasles-pillars__head">
      <p class="lasles-path-kicker">{{ __('landing.home.pillars_kicker') }}</p>
      <h2 id="pillars-title" class="lasles-section-title">{{ __('landing.home.pillars_title') }}</h2>
      <p class="lasles-section-lead">{{ __('landing.home.pillars_lead') }}</p>
    </div>
    <div class="lasles-pillars__grid">
      @foreach($services as $index => $service)
        @php
          $copy = is_array($pillarCopy) ? ($pillarCopy[$service['key']] ?? []) : [];
          $step = $copy['step'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
        @endphp
        <a class="lasles-pillar" href="{{ $service['url'] }}">
          <span class="lasles-pillar__top">
            <span class="lasles-pillar__icon" aria-hidden="true">
              <img src="{{ $img($icons[$service['key']] ?? 'icon-user.svg') }}" width="28" height="28" alt="">
            </span>
            <span class="lasles-pillar__step">{{ $step }}</span>
          </span>
          <h3>{{ $copy['title'] ?? $service['label'] }}</h3>
          <p>{{ $copy['body'] ?? '' }}</p>
          <span class="lasles-pillar__link">
            {{ __('landing.home.pillars_cta') }}
            <span class="lasles-pillar__arrow" aria-hidden="true"></span>
          </span>
        </a>
      @endforeach
    </div>
  </div>
</section>
