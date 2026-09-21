@php
    $guide = __('landing.learning_paths.guide');
    $guide = is_array($guide) ? $guide : [];
@endphp
<section class="lasles-paths-guide" aria-labelledby="paths-guide-title">
  <div class="lasles-container">
    <div class="lasles-paths-guide__head">
      <h2 id="paths-guide-title" class="lasles-section-title lasles-section-title--center">{{ __('landing.learning_paths.guide_title') }}</h2>
      <p class="lasles-section-lead lasles-section-lead--center">{{ __('landing.learning_paths.guide_lead') }}</p>
    </div>
    <ol class="lasles-paths-guide__grid">
      @foreach($guide as $i => $item)
        <li class="lasles-paths-guide__item">
          <span class="lasles-paths-guide__num" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
          <h3>{{ $item['title'] ?? '' }}</h3>
          <p>{{ $item['body'] ?? '' }}</p>
        </li>
      @endforeach
    </ol>
  </div>
</section>
