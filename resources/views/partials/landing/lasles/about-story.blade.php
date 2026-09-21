@php
    $img = $img ?? fn (string $file) => asset('img/lasles/'.$file);
    $content = $content ?? [];
    $points = $content['story_points'] ?? [];
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
@endphp
<section class="lasles-about-story" id="story">
  <div class="lasles-container lasles-about-story__grid">
    <div class="lasles-about-story__copy">
      <p class="lasles-path-kicker">{{ $content['story_kicker'] ?? '' }}</p>
      <h2 class="lasles-section-title">{{ $content['story_title'] ?? '' }}</h2>
      <p class="lasles-section-lead">{{ $content['story_body'] ?? '' }}</p>
      @if(is_array($points) && count($points))
        <ul class="lasles-checklist">
          @foreach($points as $item)
            <li><img src="{{ $img('check-green.svg') }}" width="24" height="24" alt="">{{ $item }}</li>
          @endforeach
        </ul>
      @endif
    </div>
    <aside class="lasles-about-story__panel" aria-hidden="true">
      <p class="lasles-about-story__panel-label">{{ $isRtl ? 'حلقة التطوير' : 'Growth loop' }}</p>
      <ol class="lasles-about-story__loop">
        <li><span>01</span>{{ __('landing.path.steps.diagnose.title') }}</li>
        <li><span>02</span>{{ __('landing.path.steps.access.title') }}</li>
        <li><span>03</span>{{ __('landing.path.steps.develop.title') }}</li>
        <li><span>04</span>{{ __('landing.path.steps.measure.title') }}</li>
      </ol>
    </aside>
  </div>
</section>
