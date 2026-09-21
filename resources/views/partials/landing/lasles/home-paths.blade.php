@php
    $featuredPaths = $featuredPaths ?? collect();
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
@endphp
<section class="lasles-showcase lasles-showcase--paths" id="home-paths" aria-labelledby="home-paths-title">
  <div class="lasles-container">
    <div class="lasles-showcase__head">
      <div class="lasles-showcase__intro">
        <p class="lasles-path-kicker">{{ __('landing.home.showcase_paths_kicker') }}</p>
        <h2 id="home-paths-title" class="lasles-section-title">{{ __('landing.home.showcase_paths_title') }}</h2>
        <p class="lasles-section-lead">{{ __('landing.home.showcase_paths_lead') }}</p>
      </div>
      <a href="{{ route('public.learning-paths.index') }}" class="lasles-showcase__more lasles-btn-outline">
        {{ __('landing.home.showcase_paths_cta') }}
        <span aria-hidden="true">{{ $isRtl ? '←' : '→' }}</span>
      </a>
    </div>

    @if($featuredPaths->isEmpty())
      <div class="lasles-showcase__empty">
        <p>{{ __('landing.home.showcase_paths_empty') }}</p>
        <a href="{{ route('public.learning-paths.index') }}" class="lasles-btn-primary">{{ __('landing.home.showcase_paths_cta') }}</a>
      </div>
    @else
      <div class="lasles-showcase__grid lasles-showcase__grid--paths">
        @foreach($featuredPaths as $i => $path)
          @php
            $skill = $path->skillFocus() ?: null;
            $showSkill = filled($skill) && $skill !== $path->title();
            $thumb = filled($path->thumbnail)
              ? (str_starts_with((string) $path->thumbnail, 'http') ? $path->thumbnail : asset('storage/'.$path->thumbnail))
              : null;
          @endphp
          <a href="{{ route('public.learning-paths.show', $path->slug) }}" class="lasles-path-card-pro {{ $i === 0 ? 'is-featured' : '' }}">
            <div class="lasles-path-card-pro__glow" aria-hidden="true"></div>
            @php
              $mediaStyle = $thumb ? 'style="--path-cover: url(\''.e($thumb).'\')"' : '';
            @endphp
            <div class="lasles-path-card-pro__media {{ $thumb ? '' : 'lasles-path-card-pro__media--accent' }}" {!! $mediaStyle !!}>
              @if($thumb)
                <img src="{{ $thumb }}" alt="" loading="lazy" decoding="async">
                <span class="lasles-path-card-pro__shade" aria-hidden="true"></span>
                <span class="lasles-path-card-pro__index lasles-path-card-pro__index--on-media">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
              @else
                <span class="lasles-path-card-pro__index">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
              @endif
            </div>
            <div class="lasles-path-card-pro__body">
              <div class="lasles-path-card-pro__top">
                @if($showSkill)
                  <span class="lasles-path-card-pro__skill">{{ $skill }}</span>
                @endif
                <span class="lasles-path-card-pro__meta">
                  {{ __('landing.learning_paths.units_count', ['count' => $path->units_count]) }}
                  @if($path->estimated_minutes)
                    · {{ $path->estimated_minutes }} {{ __('landing.learning_paths.minutes') }}
                  @endif
                </span>
              </div>
              <h3 class="lasles-path-card-pro__title">{{ $path->title() }}</h3>
              @if($path->summary())
                <p class="lasles-path-card-pro__summary">{{ \Illuminate\Support\Str::limit($path->summary(), 120) }}</p>
              @endif
              <span class="lasles-path-card-pro__cta">
                {{ __('landing.learning_paths.view_path') }}
                <span aria-hidden="true">{{ $isRtl ? '←' : '→' }}</span>
              </span>
            </div>
          </a>
        @endforeach
      </div>
      <div class="lasles-showcase__foot">
        <a href="{{ route('public.learning-paths.index') }}" class="lasles-showcase__more-link">
          {{ __('landing.home.showcase_paths_cta') }}
          <span aria-hidden="true">{{ $isRtl ? '←' : '→' }}</span>
        </a>
      </div>
    @endif
  </div>
</section>
