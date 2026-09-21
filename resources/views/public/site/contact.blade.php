@extends('layouts.lasles-public')

@section('content')
@php
  $isRtl = app()->getLocale() === 'ar';
  $fields = $content['fields'] ?? [];
@endphp

<section class="lasles-site-hero">
  <div class="lasles-container lasles-site-hero__inner">
    @if(!empty($content['kicker']))
      <p class="lasles-path-kicker">{{ $content['kicker'] }}</p>
    @endif
    <h1 class="lasles-site-hero__title">{{ $content['title'] ?? __('site.nav.contact') }}</h1>
    @if(!empty($content['lead']))
      <p class="lasles-site-hero__lead">{{ $content['lead'] }}</p>
    @endif
  </div>
</section>

<section class="lasles-site-contact">
  <div class="lasles-container lasles-site-contact__grid">
    <aside class="lasles-site-contact__info">
      <h2 class="lasles-section-title">{{ $isRtl ? 'بيانات التواصل' : 'Contact details' }}</h2>
      @if(!empty($content['email']))
        <p><strong>{{ $isRtl ? 'البريد' : 'Email' }}:</strong> <a href="mailto:{{ $content['email'] }}">{{ $content['email'] }}</a></p>
      @endif
      @if(!empty($content['phone']))
        <p><strong>{{ $isRtl ? 'الهاتف' : 'Phone' }}:</strong> <a dir="ltr" href="tel:{{ preg_replace('/\s+/', '', $content['phone']) }}">{{ $content['phone'] }}</a></p>
      @endif
      @if(!empty($content['region']))
        <p><strong>{{ $isRtl ? 'النطاق' : 'Region' }}:</strong> {{ $content['region'] }}</p>
      @endif
      @foreach(($content['sections'] ?? []) as $section)
        <div class="lasles-site-block" style="margin-top:1.25rem">
          <h3>{{ $section['title'] ?? '' }}</h3>
          <p>{{ $section['body'] ?? '' }}</p>
        </div>
      @endforeach
    </aside>

    <div class="lasles-site-form-card">
      @if(session('status'))
        <div class="lasles-site-alert lasles-site-alert--ok">{{ session('status') }}</div>
      @endif
      @if($errors->any())
        <div class="lasles-site-alert lasles-site-alert--err">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('public.contact.store') }}" novalidate>
        @csrf
        <div class="lasles-site-field">
          <label for="name">{{ $fields['name'] ?? 'Name' }}</label>
          <input class="lasles-site-input" type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>
        <div class="lasles-site-field">
          <label for="email">{{ $fields['email'] ?? 'Email' }}</label>
          <input class="lasles-site-input" type="email" name="email" id="email" value="{{ old('email') }}" required dir="ltr">
        </div>
        <div class="lasles-site-field">
          <label for="phone">{{ $fields['phone'] ?? 'Phone' }}</label>
          <input class="lasles-site-input" type="text" name="phone" id="phone" value="{{ old('phone') }}" dir="ltr">
        </div>
        <div class="lasles-site-field">
          <label for="inquiry_type">{{ $fields['inquiry_type'] ?? (app()->getLocale() === 'ar' ? 'نوع الاستفسار' : 'Inquiry type') }}</label>
          <select class="lasles-site-input" name="inquiry_type" id="inquiry_type">
            @foreach(\App\Models\Inquiry::typeLabels() as $key => $label)
              <option value="{{ $key }}" @selected(old('inquiry_type', request('topic')) === $key)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="lasles-site-field">
          <label for="topic">{{ $fields['topic'] ?? 'Topic' }}</label>
          <input class="lasles-site-input" type="text" name="topic" id="topic" value="{{ old('topic') }}">
        </div>
        <div class="lasles-site-field">
          <label for="message">{{ $fields['message'] ?? 'Message' }}</label>
          <textarea class="lasles-site-input" name="message" id="message" rows="5" required>{{ old('message') }}</textarea>
        </div>
        <button type="submit" class="lasles-btn-primary" style="width:100%">{{ $fields['submit'] ?? 'Send' }}</button>
      </form>
    </div>
  </div>
</section>
@endsection
