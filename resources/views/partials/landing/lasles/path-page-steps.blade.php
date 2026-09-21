@php
    $steps = $steps ?? [
        ['num' => '01', 'key' => 'diagnose'],
        ['num' => '02', 'key' => 'access'],
        ['num' => '03', 'key' => 'develop'],
        ['num' => '04', 'key' => 'measure'],
    ];
@endphp
<section class="lasles-path-steps" aria-labelledby="path-steps-title">
  <div class="lasles-container">
    <div class="lasles-path-steps__head">
      <h2 id="path-steps-title" class="lasles-section-title lasles-section-title--center">{{ __('landing.path.steps_title') }}</h2>
      <p class="lasles-section-lead lasles-section-lead--center">{{ __('landing.path.steps_lead') }}</p>
    </div>
    <ol class="lasles-path-journey">
      @foreach ($steps as $step)
        <li class="lasles-path-journey__item">
          <span class="lasles-path-journey__num" aria-hidden="true">{{ $step['num'] }}</span>
          <div class="lasles-path-journey__body">
            <h3 class="lasles-path-journey__title">{{ __('landing.path.steps.'.$step['key'].'.title') }}</h3>
            <p class="lasles-path-journey__text">{{ __('landing.path.steps.'.$step['key'].'.body') }}</p>
            <p class="lasles-path-journey__outcome">{{ __('landing.path.steps.'.$step['key'].'.outcome') }}</p>
          </div>
        </li>
      @endforeach
    </ol>
  </div>
</section>
