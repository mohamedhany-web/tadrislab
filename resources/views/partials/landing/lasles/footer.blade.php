@php
    use App\Support\TadrisPublicNav;
    use App\Services\PublicFooterSettings;
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $brand = 'TADRIS LAB';
    $brandAr = 'تدريس لاب';
    $img = $img ?? fn (string $file) => asset('img/lasles/'.$file);
    $services = TadrisPublicNav::services();
    $footer = PublicFooterSettings::payload();
    $whatsapp = $footer['whatsapp_url'] ?? '';
    $email = $footer['email'] ?? 'info@tadrislab.com';
    $phone = $footer['phone'] ?? '';
    $socials = $footer['socials'] ?? [];
    $tagline = $footer['brand_tagline'] ?? ($isRtl ? 'تطوير الممارسات المهنية للمعلمين.' : 'Professional development for teachers.');

    $socialKind = static function (string $label): string {
        $l = mb_strtolower($label);
        if (str_contains($l, 'facebook')) return 'facebook';
        if (str_contains($l, 'instagram')) return 'instagram';
        if (str_contains($l, 'youtube')) return 'youtube';
        if (str_contains($l, 'linkedin')) return 'linkedin';
        if (str_contains($l, 'tiktok')) return 'tiktok';
        if (str_contains($l, 'telegram')) return 'telegram';
        if (str_contains($l, 'snapchat')) return 'snapchat';
        if (str_contains($l, 'whatsapp')) return 'whatsapp';
        if (str_contains($l, 'twitter') || $l === 'x' || str_starts_with($l, 'x ')) return 'x';
        return 'link';
    };
@endphp
<footer class="lasles-footer">
  <div class="lasles-container lasles-footer__grid">
    <div class="lasles-footer__about">
      <a href="{{ route('home') }}" class="lasles-brand lasles-footer__brand">
        <img src="{{ $img('logo-mark.png') }}" width="36" height="36" alt="{{ $isRtl ? 'تدريس لاب' : 'TADRIS LAB' }}">
        <span>@if($isRtl)<b>تدريس لاب</b>@else<b>TADRIS</b> <em>LAB</em>@endif</span>
      </a>
      <p class="lasles-footer__blurb">{{ $tagline }}</p>

      <div class="lasles-footer__meta">
        @if($email)
          <a href="mailto:{{ $email }}" class="lasles-footer__meta-link">{{ $email }}</a>
        @endif
        @if($phone)
          <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="lasles-footer__meta-link">{{ $phone }}</a>
        @endif
      </div>

      <div class="lasles-social" aria-label="{{ $isRtl ? 'حسابات التواصل' : 'Social links' }}">
        @forelse($socials as $social)
          @php $kind = $socialKind($social['label'] ?? ''); @endphp
          <a href="{{ $social['url'] }}" target="_blank" rel="noopener" class="lasles-social__btn" aria-label="{{ $social['label'] }}">
            @include('partials.landing.lasles.social-icon', ['kind' => $kind])
          </a>
        @empty
          @if($whatsapp)
            <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="lasles-social__btn" aria-label="WhatsApp">
              @include('partials.landing.lasles.social-icon', ['kind' => 'whatsapp'])
            </a>
          @endif
          <a href="{{ route('public.contact') }}" class="lasles-social__btn" aria-label="{{ $isRtl ? 'تواصل معنا' : 'Contact' }}">
            @include('partials.landing.lasles.social-icon', ['kind' => 'mail'])
          </a>
        @endforelse
      </div>
    </div>

    <nav class="lasles-footer__cols" aria-label="{{ $isRtl ? 'روابط الفوتر' : 'Footer links' }}">
      <div>
        <h4>{{ __('site.nav_short.services') }}</h4>
        @foreach($services as $service)
          <a href="{{ $service['url'] }}">{{ $service['label'] }}</a>
        @endforeach
      </div>
      <div>
        <h4>{{ $isRtl ? 'المنصة' : 'Platform' }}</h4>
        <a href="{{ route('public.about') }}">{{ __('site.nav.about') }}</a>
        <a href="{{ route('public.contact') }}">{{ __('landing.home.cta_contact') }}</a>
        <a href="{{ route('register') }}">{{ __('landing.home.cta_start_journey') }}</a>
      </div>
      <div>
        <h4>{{ $isRtl ? 'قانوني' : 'Legal' }}</h4>
        <a href="{{ route('public.privacy') }}">{{ $isRtl ? 'سياسة الخصوصية' : 'Privacy Policy' }}</a>
        <a href="{{ route('public.terms') }}">{{ $isRtl ? 'الشروط والأحكام' : 'Terms of Service' }}</a>
        <a href="{{ route('public.faq') }}">{{ $isRtl ? 'الأسئلة الشائعة' : 'FAQ' }}</a>
      </div>
    </nav>
  </div>
  <div class="lasles-container lasles-footer__bottom">
    <p class="lasles-copy">©{{ date('Y') }} {{ $isRtl ? $brandAr : $brand }}</p>
  </div>
</footer>
