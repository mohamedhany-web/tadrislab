@extends('layouts.lasles-public')

@section('content')
@php
  $isRtl = app()->getLocale() === 'ar';
  $field = 'width:100%;padding:.65rem .85rem;border:1px solid #ddd;border-radius:10px;font-size:.95rem';
  $wallets = \App\Services\PlatformPaymentAccountService::activeAccounts();
  $remainingSessions = (int) ($remainingSessions ?? 0);
  $isFree = (float) $service->price < 0.01;
  $defaultUsePackage = ! $isFree && $remainingSessions > 0 && (string) old('use_package_session', '1') === '1';
  $paypalReady = ! empty($paypalReady);
  $kashierReady = ! empty($kashierReady);
  $defaultMethod = old('payment_method');
  if (! $defaultMethod) {
      if ($kashierReady) {
          $defaultMethod = 'kashier';
      } elseif ($paypalReady) {
          $defaultMethod = 'paypal';
      } else {
          $defaultMethod = 'bank_transfer';
      }
  }
@endphp

<section class="lasles-paths-hero lasles-paths-hero--detail">
  <div class="lasles-container lasles-paths-hero__inner">
    <p class="lasles-site-breadcrumb">
      <a href="{{ route('public.consultations.book') }}">{{ $isRtl ? 'الاستشارات' : 'Consultations' }}</a>
      <span>/</span>
      <span>{{ $service->title() }}</span>
    </p>
    <h1 class="lasles-paths-hero__title">{{ $service->title() }}</h1>
    <p class="lasles-paths-hero__skill">{{ $service->typeLabel() }}</p>
    @if($service->summary())
      <p class="lasles-paths-hero__lead">{{ $service->summary() }}</p>
    @endif
    <p class="lasles-paths-hero__skill" style="margin-top:1rem">
      @if($isFree)
        {{ $isRtl ? 'مجاني' : 'Free' }}
      @else
        {{ number_format((float) $service->price, 2) }} {{ $service->currency }}
      @endif
      · {{ (int) $service->duration_minutes }} {{ $isRtl ? 'دقيقة' : 'min' }}
    </p>
  </div>
</section>

<section class="lasles-container" style="padding:1.5rem 0 4rem;max-width:40rem">
  @if($errors->any())
    <div style="margin-bottom:1rem;padding:1rem;border:1px solid #f5c2c7;background:#f8d7da;border-radius:10px;font-size:.9rem">
      <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  @guest
    <p style="margin-bottom:1.5rem">
      <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="lasles-btn-primary">{{ $isRtl ? 'سجّل الدخول لإكمال الحجز' : 'Log in to book' }}</a>
    </p>
  @endguest

  <form method="POST" action="{{ route('public.consultations.book.store', $service->slug) }}" enctype="multipart/form-data" style="display:grid;gap:1rem" id="consultation-book-form">
    @csrf

    @if($service->requires_instructor)
      <div>
        <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'المستشار / المدرب' : 'Consultant' }} *</label>
        <select name="instructor_id" required style="{{ $field }}">
          <option value="">—</option>
          @foreach($instructors as $ins)
            <option value="{{ $ins->id }}" @selected(old('instructor_id', $instructor?->id) == $ins->id)>{{ $ins->name }}</option>
          @endforeach
        </select>
      </div>
    @endif

    @if($slots->isNotEmpty())
      <div>
        <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'موعد مقترح متاح' : 'Preferred available slot' }}</label>
        <select name="preferred_slot_at" style="{{ $field }}">
          <option value="">{{ $isRtl ? '— اختياري / يحدد لاحقًا —' : '— optional —' }}</option>
          @foreach($slots as $slot)
            <option value="{{ $slot['starts_at']->toIso8601String() }}" @selected(old('preferred_slot_at') === $slot['starts_at']->toIso8601String())>
              {{ $slot['label'] }}
            </option>
          @endforeach
        </select>
      </div>
    @else
      <div>
        <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'موعد مقترح (اختياري)' : 'Preferred datetime (optional)' }}</label>
        <input type="datetime-local" name="preferred_slot_at" value="{{ old('preferred_slot_at') }}" style="{{ $field }}">
        <p style="font-size:.75rem;opacity:.7;margin-top:.35rem">{{ $isRtl ? 'إن لم تتوفر نوافذ المدرب، تؤكد الإدارة الموعد.' : 'Admin will confirm the final slot.' }}</p>
      </div>
    @endif

    <div>
      <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'الاسم' : 'Name' }} *</label>
      <input type="text" name="contact_name" required value="{{ old('contact_name', auth()->user()->name ?? '') }}" style="{{ $field }}">
    </div>
    <div>
      <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'واتساب' : 'WhatsApp' }} *</label>
      <input type="text" name="contact_phone" required value="{{ old('contact_phone', auth()->user()->phone ?? '') }}" style="{{ $field }}">
    </div>
    <div>
      <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'البريد' : 'Email' }} *</label>
      <input type="email" name="contact_email" required value="{{ old('contact_email', auth()->user()->email ?? '') }}" style="{{ $field }}">
    </div>
    @if($service->consultation_type === 'institution')
      <div>
        <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'اسم المدرسة / المؤسسة' : 'School / institution' }} *</label>
        <input type="text" name="organization_name" required value="{{ old('organization_name') }}" style="{{ $field }}">
      </div>
    @endif
    <div>
      <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'ملاحظات / موضوع الاستشارة' : 'Notes / topic' }}</label>
      <textarea name="student_message" rows="3" style="{{ $field }}">{{ old('student_message') }}</textarea>
    </div>

    @if(! $isFree && $remainingSessions > 0)
      <fieldset style="border:1px solid #c7e0f5;border-radius:12px;padding:1rem;background:#f4f9fd">
        <legend style="font-size:.85rem;font-weight:700;padding:0 .5rem">{{ $isRtl ? 'رصيد الباقة' : 'Package balance' }}</legend>
        <p style="margin:0 0 .75rem;font-size:.9rem;color:#334155">
          {{ $isRtl ? 'لديك' : 'You have' }}
          <strong>{{ $remainingSessions }}</strong>
          {{ $isRtl ? 'جلسة استشارة متبقية في باقتك.' : 'consultation session(s) left in your package.' }}
        </p>
        <label style="display:flex;align-items:flex-start;gap:.5rem;font-size:.9rem;cursor:pointer">
          <input type="hidden" name="use_package_session" value="0">
          <input type="checkbox" name="use_package_session" value="1" id="use_package_session" @checked($defaultUsePackage) style="margin-top:.2rem">
          <span>{{ $isRtl ? 'استخدم جلسة من الباقة (بدون دفع إضافي)' : 'Use a package session (no extra payment)' }}</span>
        </label>
      </fieldset>
    @else
      <input type="hidden" name="use_package_session" value="0">
    @endif

    @if($isFree)
      <input type="hidden" name="payment_method" value="other">
      <div style="padding:1rem;border-radius:12px;background:#ecfdf5;border:1px solid #a7f3d0;font-size:.9rem">
        {{ $isRtl ? 'هذه الخدمة مجانية — اضغط إرسال الحجز وسنؤكّد الموعد من الإدارة.' : 'This service is free — submit and admin will confirm the slot.' }}
      </div>
    @else
      <fieldset id="payment-fieldset" style="border:1px solid #eee;border-radius:12px;padding:1rem{{ $defaultUsePackage ? ';display:none' : '' }}">
        <legend style="font-size:.85rem;font-weight:700;padding:0 .5rem">{{ $isRtl ? 'الدفع' : 'Payment' }}</legend>
        <p style="font-size:.85rem;opacity:.8;margin:0 0 .85rem">{{ $isRtl ? 'ادفع أونلاين فورًا أو أرسل تحويلًا بنكيًا للمراجعة.' : 'Pay online now, or send a bank transfer for review.' }}</p>

        <div style="display:grid;gap:.65rem;margin-bottom:1rem" role="radiogroup">
          @if($kashierReady)
            <label style="display:flex;gap:.65rem;align-items:flex-start;padding:.75rem;border:1px solid #e2e8f0;border-radius:10px;cursor:pointer">
              <input type="radio" name="payment_method" value="kashier" @checked($defaultMethod === 'kashier') style="margin-top:.2rem">
              <span>
                <strong style="display:block">{{ $isRtl ? 'كاشير (بطاقة / محفظة)' : 'Kashier (card / wallet)' }}</strong>
                <em style="font-size:.8rem;opacity:.75;font-style:normal">{{ $isRtl ? 'دفع فوري وتفعيل الحجز بعد النجاح' : 'Instant pay — booking marked paid on success' }}</em>
              </span>
            </label>
          @endif
          @if($paypalReady)
            <label style="display:flex;gap:.65rem;align-items:flex-start;padding:.75rem;border:1px solid #e2e8f0;border-radius:10px;cursor:pointer">
              <input type="radio" name="payment_method" value="paypal" @checked($defaultMethod === 'paypal') style="margin-top:.2rem">
              <span>
                <strong style="display:block">PayPal</strong>
                <em style="font-size:.8rem;opacity:.75;font-style:normal">{{ $isRtl ? 'دفع فوري عبر PayPal' : 'Instant PayPal checkout' }}</em>
              </span>
            </label>
          @endif
          <label style="display:flex;gap:.65rem;align-items:flex-start;padding:.75rem;border:1px solid #e2e8f0;border-radius:10px;cursor:pointer">
            <input type="radio" name="payment_method" value="bank_transfer" id="pay_bank" @checked($defaultMethod === 'bank_transfer') style="margin-top:.2rem">
            <span>
              <strong style="display:block">{{ $isRtl ? 'تحويل بنكي / محفظة' : 'Bank / wallet transfer' }}</strong>
              <em style="font-size:.8rem;opacity:.75;font-style:normal">{{ $isRtl ? 'أرفق الإيصال — التفعيل بعد مراجعة الإدارة' : 'Attach proof — activated after admin review' }}</em>
            </span>
          </label>
        </div>

        <div id="bank-transfer-fields" style="{{ $defaultMethod === 'bank_transfer' ? '' : 'display:none' }}">
          @if($wallets->isNotEmpty())
            <select name="wallet_id" style="{{ $field }};margin-bottom:.75rem">
              <option value="">{{ $isRtl ? 'حساب التحويل' : 'Transfer account' }}</option>
              @foreach($wallets as $w)
                <option value="{{ $w->id }}" @selected(old('wallet_id')==$w->id)>{{ $w->checkoutLabel() }}</option>
              @endforeach
            </select>
          @endif
          <label style="display:block;font-size:.8rem;margin-bottom:.35rem">{{ $isRtl ? 'إيصال التحويل *' : 'Transfer proof *' }}</label>
          <input type="file" name="payment_proof" id="payment_proof" accept="image/*,.pdf" style="{{ $field }}">
          <input type="text" name="payment_reference" placeholder="{{ $isRtl ? 'مرجع التحويل (اختياري)' : 'Reference (optional)' }}" value="{{ old('payment_reference') }}" style="{{ $field }};margin-top:.75rem">
        </div>
      </fieldset>
    @endif

    <button type="submit" class="lasles-btn-primary" @guest disabled @endguest>
      @if($isFree)
        {{ $isRtl ? 'إرسال الحجز' : 'Submit booking' }}
      @else
        {{ $isRtl ? 'إكمال الحجز والدفع' : 'Complete booking & pay' }}
      @endif
    </button>
  </form>
</section>

@push('scripts')
<script>
(function () {
  var box = document.getElementById('use_package_session');
  var pay = document.getElementById('payment-fieldset');
  var bankFields = document.getElementById('bank-transfer-fields');
  var proof = document.getElementById('payment_proof');

  function selectedMethod() {
    var el = document.querySelector('input[name="payment_method"]:checked');
    return el ? el.value : '';
  }

  function syncBank() {
    var isBank = selectedMethod() === 'bank_transfer';
    if (bankFields) bankFields.style.display = isBank ? '' : 'none';
    if (proof) proof.required = isBank && !(box && box.checked);
  }

  function syncPackage() {
    if (!pay) return;
    var usePkg = box && box.checked;
    pay.style.display = usePkg ? 'none' : '';
    syncBank();
  }

  document.querySelectorAll('input[name="payment_method"]').forEach(function (r) {
    r.addEventListener('change', syncBank);
  });
  if (box) box.addEventListener('change', syncPackage);
  syncPackage();
})();
</script>
@endpush
@endsection
