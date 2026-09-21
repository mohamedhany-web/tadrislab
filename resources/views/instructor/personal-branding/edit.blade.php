@extends('layouts.instructor-timeline')

@section('title', __('instructor.personal_branding') . ' - ' . config('app.name'))
@section('page_title', __('instructor.personal_branding'))

@section('content')
@php
    $plLocale = app()->getLocale() === 'ar' ? 'ar' : 'en';
    $statusChip = match ($profile->status) {
        'approved' => 'su-chip--ok',
        'pending_review' => 'su-chip--warn',
        'rejected' => 'su-chip--off',
        default => '',
    };
@endphp
<div class="su-page" style="max-width:56rem">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-id-badge su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.profile_branding_title') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.personal_branding_desc') }}</p>
            <div class="su-chip-row">
                <span class="su-chip {{ $statusChip }}">
                    {{ __('instructor.status_label') }}: {{ \App\Models\InstructorProfile::statusLabel($profile->status) }}
                </span>
            </div>
            @if($profile->rejection_reason)
                <p style="margin:8px 0 0;font-size:13px;color:#b91c1c">{{ __('instructor.rejection_reason_label') }}: {{ $profile->rejection_reason }}</p>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            <i class="fas fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#b91c1c;font-size:13px">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ session('error') }}
        </div>
    @endif

    <section class="su-card" style="margin-bottom:16px">
        <form method="POST" action="{{ route('instructor.personal-branding.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($profile->status === \App\Models\InstructorProfile::STATUS_APPROVED)
                <div class="su-card" style="margin-bottom:16px;padding:12px 16px;background:rgba(59,130,246,.08);border-color:transparent;font-size:13px">
                    {{ __('instructor.profile_approved_public') }}
                    <a href="{{ route('public.instructors.show', $profile->user_id) }}" target="_blank" rel="noopener" style="font-weight:700;text-decoration:underline">
                        {{ __('instructor.view_public_profile') }}
                    </a>
                </div>
            @endif

            <div class="su-form-grid" style="grid-template-columns:1fr">
                <div class="su-field">
                    <label>{{ __('instructor.profile_photo') }}</label>
                    @if($profile->photo_path)
                        <div style="width:96px;height:96px;border-radius:12px;overflow:hidden;border:1px solid var(--su-line,rgba(0,0,0,.08));margin-bottom:8px;position:relative;background:var(--su-soft-1,rgba(0,0,0,.04))">
                            <img src="{{ $profile->photo_url }}" alt="{{ __('instructor.profile_photo_alt') }}" style="width:100%;height:100%;object-fit:cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                            <div class="hidden" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:var(--su-ink-40)"><i class="fas fa-user" style="font-size:28px" aria-hidden="true"></i></div>
                        </div>
                    @endif
                    <input type="file" name="photo" accept="image/*" class="su-input">
                    @error('photo')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>

                <div class="su-field">
                    <label>{{ __('instructor.intro_video_url') }}</label>
                    <input type="url" name="intro_video_url" value="{{ old('intro_video_url', auth()->user()->portfolio_intro_video_url) }}" dir="ltr" class="su-input"
                           placeholder="https://youtube.com/...">
                    <span style="font-size:12px;color:var(--su-ink-40)">{{ __('instructor.intro_video_hint') }}</span>
                    @error('intro_video_url')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>

                <div class="su-field">
                    <label>{{ __('instructor.intro_title') }}</label>
                    <input type="text" name="headline" value="{{ old('headline', $profile->headline) }}" class="su-input"
                           placeholder="{{ __('instructor.headline_placeholder') }}">
                    @error('headline')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>

                <div class="su-field">
                    <label>{{ __('instructor.bio') }}</label>
                    <textarea name="bio" rows="5" class="su-input" style="min-height:120px;resize:vertical"
                              placeholder="{{ __('instructor.bio_placeholder') }}">{{ old('bio', $profile->bio) }}</textarea>
                    @error('bio')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>

                <div class="su-field">
                    <label>{{ __('instructor.experience') }}</label>
                    <textarea name="experience" rows="8" class="su-input" style="min-height:160px;resize:vertical"
                              placeholder="{{ __('instructor.experience_placeholder') }}">{{ old('experience', $profile->experience) }}</textarea>
                    @error('experience')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>

                <div class="su-field">
                    <label>{{ __('instructor.skills') }}</label>
                    <p style="margin:0 0 6px;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.skills_hint') }}</p>
                    <textarea name="skills" rows="5" class="su-input" style="min-height:110px;resize:vertical"
                              placeholder="{{ __('instructor.skills_placeholder') }}">{{ old('skills', $profile->skills) }}</textarea>
                    @error('skills')<p class="su-field-error">{{ $message }}</p>@enderror
                    @php
                        $skillsPreview = $profile->skills_list;
                        if (old('skills') !== null) {
                            $split = preg_split('/[\r\n,،]+/u', old('skills'), -1, PREG_SPLIT_NO_EMPTY);
                            $skillsPreview = array_values(array_filter(array_map('trim', $split)));
                        }
                    @endphp
                    @if(count($skillsPreview) > 0)
                        <p style="margin:8px 0 4px;font-size:12px;color:var(--su-ink-40)">
                            {{ __('instructor.skills_preview') }} ({{ count($skillsPreview) }} {{ __('instructor.skill_count') }}):
                        </p>
                        <div class="su-chip-row">
                            @foreach($skillsPreview as $skill)
                                <span class="su-chip su-soft-1">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @php
                $plMeta = old('private_subjects') !== null
                    ? [
                        'subjects' => old('private_subjects', []),
                        'age_groups' => old('private_age_groups', []),
                        'languages' => old('private_languages', []),
                        'specializations' => old('private_specializations', []),
                    ]
                    : (auth()->user()->privateTeachingMeta());
                $plGender = old('gender', auth()->user()->gender);
            @endphp
            <div class="su-card" style="margin:20px 0;background:rgba(11,61,145,.04)">
                <h3 class="su-card__title">{{ __('instructor.private_lessons_profile') }}</h3>
                <p style="margin:0 0 14px;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.private_lessons_profile_desc') }}</p>

                <div class="su-field" style="margin-bottom:14px">
                    <label>{{ __('instructor.teacher_gender') }}</label>
                    <div class="su-chip-row">
                        @foreach(config('private_lessons.genders') as $gKey => $gLabels)
                            <label class="su-chip" style="cursor:pointer">
                                <input type="radio" name="gender" value="{{ $gKey }}" @checked($plGender === $gKey) style="margin-inline-end:6px">
                                {{ $gLabels[$plLocale] ?? $gKey }}
                            </label>
                        @endforeach
                    </div>
                </div>

                @foreach([
                    'private_subjects' => ['subjects', 'subjects'],
                    'private_age_groups' => ['age_groups', 'age_groups'],
                    'private_languages' => ['languages', 'languages'],
                    'private_specializations' => ['specializations', 'specializations'],
                ] as $inputName => [$configKey, $metaKey])
                    <div class="su-field" style="margin-bottom:14px">
                        <label>{{ __('public.private_filter_'.($configKey === 'age_groups' ? 'age' : ($configKey === 'specializations' ? 'specialty' : rtrim($configKey,'s')))) }}</label>
                        <div class="su-chip-row">
                            @foreach(config('private_lessons.'.$configKey, []) as $optKey => $labels)
                                <label class="su-chip" style="cursor:pointer">
                                    <input type="checkbox" name="{{ $inputName }}[]" value="{{ $optKey }}" @checked(in_array($optKey, $plMeta[$metaKey] ?? [], true)) style="margin-inline-end:6px">
                                    {{ $labels[$plLocale] ?? $optKey }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="su-card" style="margin-bottom:20px">
                <h3 class="su-card__title">{{ __('instructor.consultations_optional') }}</h3>
                <p style="margin:0 0 14px;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.consultations_optional_desc') }}</p>
                <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
                    <div class="su-field">
                        <label>{{ __('instructor.price_egp') }}</label>
                        <input type="number" step="0.01" min="0" max="999999.99" name="consultation_price_egp" value="{{ old('consultation_price_egp', $profile->consultation_price_egp) }}" class="su-input" dir="ltr">
                        @error('consultation_price_egp')<p class="su-field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="su-field">
                        <label>{{ __('instructor.duration_minutes_label') }}</label>
                        <input type="number" min="15" max="480" name="consultation_duration_minutes" value="{{ old('consultation_duration_minutes', $profile->consultation_duration_minutes) }}" class="su-input" dir="ltr">
                        @error('consultation_duration_minutes')<p class="su-field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="su-form-actions">
                <button type="submit" class="su-btn su-btn--primary">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    {{ __('instructor.save_changes') }}
                </button>
            </div>
        </form>
    </section>

    @if(in_array($profile->status, ['draft', 'rejected']))
        <form method="POST" action="{{ route('instructor.personal-branding.submit') }}">
            @csrf
            <button type="submit" class="su-btn su-btn--primary">
                <i class="fas fa-paper-plane" aria-hidden="true"></i>
                {{ __('instructor.submit_for_review') }}
            </button>
        </form>
    @endif
</div>
@endsection
