<section class="lasles-paths-catalog" id="paths-catalog" aria-labelledby="paths-catalog-title">
  <div class="lasles-container">
    <div class="lasles-paths-catalog__head">
      <div>
        <h2 id="paths-catalog-title" class="lasles-section-title">{{ __('landing.learning_paths.catalog_title') }}</h2>
        <p class="lasles-section-lead">{{ __('landing.learning_paths.catalog_lead') }}</p>
      </div>
      @if($paths->isNotEmpty())
        <p class="lasles-paths-catalog__count">{{ str_replace('{count}', (string) $paths->count(), __('landing.learning_paths.catalog_count')) }}</p>
      @endif
    </div>

    @if($paths->isEmpty())
      <div class="lasles-paths-empty">
        <h3>{{ __('landing.learning_paths.empty_title') }}</h3>
        <p>{{ __('landing.learning_paths.empty_body') }}</p>
        <a href="{{ route('public.pricing') }}" class="lasles-btn-outline">{{ __('landing.learning_paths.empty_cta') }}</a>
      </div>
    @else
      <div class="lasles-paths-grid">
        @foreach($paths as $i => $path)
          @php
            $skill = $path->skillFocus() ?: __('landing.learning_paths.skill_fallback');
            $showSkill = filled($skill) && $skill !== $path->title();
            $thumb = filled($path->thumbnail)
              ? (str_starts_with((string) $path->thumbnail, 'http') ? $path->thumbnail : asset('storage/'.$path->thumbnail))
              : null;
          @endphp
          <a href="{{ route('public.learning-paths.show', $path->slug) }}" class="lasles-path-tile {{ $thumb ? 'has-media' : '' }}">
            @if($thumb)
              <div class="lasles-path-tile__media">
                <img src="{{ $thumb }}" alt="" loading="lazy" decoding="async">
                <span class="lasles-path-tile__shade" aria-hidden="true"></span>
                <span class="lasles-path-tile__index lasles-path-tile__index--on-media" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
              </div>
            @endif
            <div class="lasles-path-tile__body">
              <div class="lasles-path-tile__top">
                @unless($thumb)
                  <span class="lasles-path-tile__index" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                @endunless
                @if($showSkill)
                  <span class="lasles-path-tile__skill">{{ $skill }}</span>
                @endif
              </div>
              <h3 class="lasles-path-tile__title">{{ $path->title() }}</h3>
              @if($path->summary())
                <p class="lasles-path-tile__summary">{{ $path->summary() }}</p>
              @endif
              <span class="lasles-path-tile__meta">
                {{ __('landing.learning_paths.units_count', ['count' => $path->units_count]) }}
                @if($path->estimated_minutes)
                  · {{ $path->estimated_minutes }} {{ __('landing.learning_paths.minutes') }}
                @endif
              </span>
              <span class="lasles-path-tile__cta">{{ __('landing.learning_paths.view_path') }} <span aria-hidden="true">→</span></span>
            </div>
          </a>
        @endforeach
      </div>
    @endif
  </div>
</section>
