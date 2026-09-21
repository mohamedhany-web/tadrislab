@extends('layouts.lasles-public')

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; $field = 'width:100%;padding:.65rem .85rem;border:1px solid #ddd;border-radius:10px;font-size:.95rem'; @endphp

<section class="lasles-paths-hero lasles-paths-hero--detail">
  <div class="lasles-container lasles-paths-hero__inner">
    <p class="lasles-paths-hero__brand">{{ $isRtl ? 'تدريس لاب' : 'TADRIS LAB' }}</p>
    <h1 class="lasles-paths-hero__title">{{ $isRtl ? 'المدارس والمؤسسات' : 'Schools & Institutions' }}</h1>
    <p class="lasles-paths-hero__lead">
      {{ $isRtl
        ? 'محور واحد للمدارس والمراكز والمؤسسات. ابدأ بطلب (Inquiry) وسنعدّ عرضًا (Proposal) ثم الاتفاق والتنفيذ.'
        : 'One axis for schools, centers, and institutions. Start with an Inquiry — we prepare a Proposal, then delivery.' }}
    </p>
  </div>
</section>

<section class="lasles-container" style="padding:1.5rem 0 4rem;max-width:40rem">
  @if(session('success'))
    <div style="margin-bottom:1rem;padding:1rem;border-radius:10px;background:#e8f7ef;border:1px solid #b7e4c7;font-size:.9rem">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div style="margin-bottom:1rem;padding:1rem;border-radius:10px;background:#f8d7da;border:1px solid #f5c2c7;font-size:.9rem">
      <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <form method="POST" action="{{ route('public.institutions.inquiry.store') }}" style="display:grid;gap:1rem">
    @csrf
    <div>
      <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'اسم الجهة *' : 'Organization *' }}</label>
      <input type="text" name="org_name" required value="{{ old('org_name') }}" style="{{ $field }}">
    </div>
    <div>
      <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'نوع الجهة *' : 'Type *' }}</label>
      <select name="org_type" required style="{{ $field }}">
        @foreach($orgTypes as $key => $label)
          <option value="{{ $key }}" @selected(old('org_type','school')===$key)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'الخدمة المطلوبة *' : 'Service *' }}</label>
      <select name="service_key" required style="{{ $field }}">
        @foreach($serviceLabels as $key => $label)
          <option value="{{ $key }}" @selected(old('service_key')===$key)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div style="display:grid;gap:1rem;grid-template-columns:1fr 1fr">
      <div>
        <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'عدد المشاركين' : 'Participants' }}</label>
        <input type="number" name="planned_participants" min="1" value="{{ old('planned_participants') }}" style="{{ $field }}">
      </div>
      <div>
        <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'طريقة التنفيذ' : 'Delivery' }}</label>
        <select name="delivery_mode" style="{{ $field }}">
          <option value="">—</option>
          @foreach($deliveryModes as $key => $label)
            <option value="{{ $key }}" @selected(old('delivery_mode')===$key)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div>
      <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'اسم المسؤول *' : 'Contact name *' }}</label>
      <input type="text" name="contact_name" required value="{{ old('contact_name') }}" style="{{ $field }}">
    </div>
    <div>
      <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'البريد *' : 'Email *' }}</label>
      <input type="email" name="contact_email" required value="{{ old('contact_email') }}" style="{{ $field }}">
    </div>
    <div>
      <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'واتساب *' : 'WhatsApp *' }}</label>
      <input type="text" name="contact_phone" required value="{{ old('contact_phone') }}" style="{{ $field }}">
    </div>
    <div style="display:grid;gap:1rem;grid-template-columns:1fr 1fr">
      <div>
        <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'المدينة' : 'City' }}</label>
        <input type="text" name="city" value="{{ old('city') }}" style="{{ $field }}">
      </div>
      <div>
        <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'الدولة' : 'Country' }}</label>
        <input type="text" name="country" value="{{ old('country') }}" style="{{ $field }}">
      </div>
    </div>
    <div>
      <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'الاحتياج / الرسالة' : 'Needs / message' }}</label>
      <textarea name="message" rows="4" style="{{ $field }}">{{ old('message') }}</textarea>
    </div>
    <button type="submit" class="lasles-btn-primary">{{ $isRtl ? 'إرسال الطلب (Inquiry)' : 'Submit Inquiry' }}</button>
  </form>
</section>
@endsection
