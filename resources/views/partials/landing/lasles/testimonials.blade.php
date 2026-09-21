@php $reviews = __('landing.home.reviews'); @endphp
<section class="lasles-reviews" id="testimonials">
  <div class="lasles-container">
    <div class="lasles-trust">
      <p class="lasles-trust__title">{{ __('landing.home.trust_logos_title') }}</p>
    </div>
    <h2 class="lasles-section-title">{{ __('landing.home.reviews_title') }}</h2>
    <p class="lasles-section-lead">{{ __('landing.home.reviews_lead') }}</p>
    <div class="lasles-reviews__track">
      @foreach($reviews as $i => $review)
        <article class="lasles-review {{ $i === 0 ? 'is-active' : '' }}">
          <div class="lasles-review__top">
            <img src="{{ $img('avatar-'.(($i % 3) + 1).'.png') }}" width="50" height="50" alt="">
            <div>
              <p class="lasles-review__name">{{ $review['name'] }}</p>
              <p class="lasles-review__loc">{{ $review['role'] }}</p>
            </div>
            <div class="lasles-review__rating">5.0 <img src="{{ $img('star.svg') }}" width="14" height="14" alt=""></div>
          </div>
          <p class="lasles-review__quote">“{{ $review['quote'] }}”</p>
        </article>
      @endforeach
    </div>
    <div class="lasles-reviews__controls">
      <div class="lasles-dots" aria-hidden="true">
        <span class="is-on"></span><span></span><span></span>
      </div>
      <div class="lasles-reviews__arrows">
        <button type="button" aria-label="{{ app()->getLocale() === 'ar' ? 'السابق' : 'Previous' }}"><img src="{{ $img('arrow-left.svg') }}" width="50" height="50" alt=""></button>
        <button type="button" aria-label="{{ app()->getLocale() === 'ar' ? 'التالي' : 'Next' }}"><img src="{{ $img('arrow-right.svg') }}" width="50" height="50" alt=""></button>
      </div>
    </div>
  </div>
</section>
