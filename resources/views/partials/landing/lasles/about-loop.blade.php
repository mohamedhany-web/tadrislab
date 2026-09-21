@php
    $content = $content ?? [];
    $steps = [
        ['num' => '01', 'key' => 'diagnose'],
        ['num' => '02', 'key' => 'access'],
        ['num' => '03', 'key' => 'develop'],
        ['num' => '04', 'key' => 'measure'],
    ];
@endphp
<section class="lasles-about-loop" id="how">
  <div class="lasles-container">
    <div class="lasles-about-loop__head">
      <h2 class="lasles-section-title lasles-section-title--center">{{ $content['loop_title'] ?? '' }}</h2>
      <p class="lasles-section-lead lasles-section-lead--center">{{ $content['loop_lead'] ?? '' }}</p>
    </div>
    <ol class="lasles-path-grid lasles-path-grid--home">
      @foreach ($steps as $step)
        <li class="lasles-path-card lasles-path-card--compact">
          <span class="lasles-path-card__num" aria-hidden="true">{{ $step['num'] }}</span>
          <h3 class="lasles-path-card__title">{{ __('landing.path.steps.'.$step['key'].'.title') }}</h3>
          <p class="lasles-path-card__body">{{ __('landing.path.steps.'.$step['key'].'.short') }}</p>
        </li>
      @endforeach
    </ol>
  </div>
</section>
