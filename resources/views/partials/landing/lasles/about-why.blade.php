@php
    $content = $content ?? [];
    $principles = $content['principles'] ?? [];
@endphp
<section class="lasles-about-why" id="why">
  <div class="lasles-container">
    <div class="lasles-about-why__head">
      <h2 class="lasles-section-title lasles-section-title--center">{{ $content['why_title'] ?? '' }}</h2>
      <p class="lasles-section-lead lasles-section-lead--center">{{ $content['why_lead'] ?? '' }}</p>
    </div>
    @if(is_array($principles) && count($principles))
      <div class="lasles-about-why__grid">
        @foreach($principles as $i => $item)
          <article class="lasles-about-principle">
            <span class="lasles-about-principle__num" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
            <h3>{{ $item['title'] ?? '' }}</h3>
            <p>{{ $item['body'] ?? '' }}</p>
          </article>
        @endforeach
      </div>
    @endif
  </div>
</section>
