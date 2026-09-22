@extends('layouts.admin')

@section('title', $package->name . ' - ' . config('app.name'))
@section('page_title', 'تفاصيل الباقة')

@section('content')
@php
    $ctaLabels = [
        'register' => 'تسجيل / اشتراك',
        'contact' => 'تواصل لعرض مؤسسي',
        'quote' => 'طلب عرض سعر',
    ];
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">الباقات · تدريس لاب</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink">{{ $package->name }}</h2>
            @if($package->trackLabel())
                <p class="mt-1 text-sm text-muted">النوع: {{ $package->trackLabel() }}</p>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.packages.edit', $package) }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-edit text-xs"></i> تعديل
            </a>
            <a href="{{ route('admin.packages.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm text-ink-soft">رجوع</a>
        </div>
    </section>

    <div class="grid gap-5 lg:grid-cols-3">
        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft lg:col-span-1">
            @if($package->thumbnail)
                <img src="{{ storage_asset($package->thumbnail) }}" alt="" class="mb-4 h-48 w-full rounded-xl object-cover border border-line">
            @else
                <div class="mb-4 flex h-48 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-box text-3xl"></i>
                </div>
            @endif
            <div class="space-y-3">
                <div>
                    <p class="text-xs font-medium text-muted">السعر</p>
                    <p class="mt-1 text-2xl font-semibold tabular-nums text-ink">{{ $package->formattedPrice(2) }}</p>
                    @if($package->formattedOriginalPrice(2))
                        <p class="text-sm text-muted line-through">{{ $package->formattedOriginalPrice(2) }}</p>
                        <p class="text-sm font-medium text-emerald-700">خصم {{ $package->discount_percentage }}%</p>
                    @endif
                    @if(filled($package->discount_note))
                        <p class="mt-1 text-sm text-muted">{{ $package->discount_note }}</p>
                    @endif
                </div>
                @php $bundleSave = $package->coursesBundleSavings(); @endphp
                @if($bundleSave > 0)
                    <div class="rounded-xl border border-accent/20 bg-accent-soft/40 px-3 py-2 text-sm">
                        توفير مقابل مجموع البرامج:
                        <span class="font-semibold tabular-nums">{{ number_format($bundleSave, 2) }} {{ $package->currencyCode() }}</span>
                    </div>
                @endif
                <div class="flex flex-wrap gap-1">
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $package->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                        {{ $package->is_active ? 'نشط' : 'معطّل' }}
                    </span>
                    @if($package->is_featured)
                        <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-800">مميز</span>
                    @endif
                    @if($package->is_popular)
                        <span class="inline-flex rounded-full bg-accent-soft px-2.5 py-0.5 text-xs font-medium text-accent">الأكثر شعبية</span>
                    @endif
                    @if($package->includes_tools)
                        <span class="inline-flex rounded-full bg-[#f2f5f4] px-2.5 py-0.5 text-xs font-medium text-ink-soft">أدوات وموارد</span>
                    @endif
                </div>
            </div>
        </article>

        <div class="space-y-5 lg:col-span-2">
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-sm font-semibold text-ink">الوصف</h3>
                <p class="mt-2 whitespace-pre-line text-sm text-muted">{{ $package->description ?: 'لا يوجد وصف' }}</p>
                @if(filled($package->card_summary))
                    <h3 class="mt-5 text-sm font-semibold text-ink">نص البطاقة</h3>
                    <p class="mt-2 whitespace-pre-line text-sm text-muted">{{ $package->card_summary }}</p>
                @endif
                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-xl border border-line px-3 py-2">
                        <p class="text-xs text-muted">العملة</p>
                        <p class="mt-1 font-semibold text-ink">{{ $package->currencyCode() }}</p>
                    </div>
                    <div class="rounded-xl border border-line px-3 py-2">
                        <p class="text-xs text-muted">مدة الصلاحية</p>
                        <p class="mt-1 font-semibold text-ink">{{ $package->duration_days ? $package->duration_days.' يوم' : 'دائمة' }}</p>
                    </div>
                    <div class="rounded-xl border border-line px-3 py-2">
                        <p class="text-xs text-muted">جلسات الاستشارة</p>
                        <p class="mt-1 font-semibold tabular-nums text-ink">{{ $package->consultation_sessions ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-line px-3 py-2">
                        <p class="text-xs text-muted">المقاعد / المشاركون</p>
                        <p class="mt-1 font-semibold tabular-nums text-ink">{{ $package->participant_seats ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-line px-3 py-2">
                        <p class="text-xs text-muted">مسارات تعليمية</p>
                        <p class="mt-1 font-semibold tabular-nums text-ink">{{ $package->learningPaths->count() }}</p>
                    </div>
                    <div class="rounded-xl border border-line px-3 py-2">
                        <p class="text-xs text-muted">زر الدعوة</p>
                        <p class="mt-1 font-semibold text-ink">{{ $ctaLabels[$package->cta_mode] ?? ($package->cta_mode ?: '—') }}</p>
                    </div>
                </div>
                @if($package->starts_at || $package->ends_at)
                    <div class="mt-4 flex flex-wrap gap-4 text-xs text-muted">
                        @if($package->starts_at)
                            <span>يبدأ: {{ $package->starts_at->format('Y-m-d') }}</span>
                        @endif
                        @if($package->ends_at)
                            <span>ينتهي: {{ $package->ends_at->format('Y-m-d') }}</span>
                        @endif
                    </div>
                @endif
            </article>

            @if(session('success'))
                <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger">
                    <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            @if($package->features && count($package->features) > 0)
                <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                    <h3 class="text-sm font-semibold text-ink">المزايا</h3>
                    <ul class="mt-3 space-y-2">
                        @foreach($package->features as $feature)
                            <li class="flex items-start gap-2 text-sm text-ink">
                                <i class="fas fa-check-circle mt-0.5 text-accent"></i>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </article>
            @endif

            @if($package->tools_resources && count($package->tools_resources) > 0)
                <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                    <h3 class="text-sm font-semibold text-ink">الأدوات والموارد (نصوص البطاقة)</h3>
                    <ul class="mt-3 space-y-2">
                        @foreach($package->tools_resources as $tool)
                            <li class="flex items-start gap-2 text-sm text-ink">
                                <i class="fas fa-wrench mt-0.5 text-accent"></i>
                                <span>{{ $tool }}</span>
                            </li>
                        @endforeach
                    </ul>
                </article>
            @endif

            @if($package->relationLoaded('teacherTools') ? $package->teacherTools->isNotEmpty() : $package->teacherTools()->exists())
                <article class="rounded-2xl border border-accent/20 bg-accent-soft/20 p-5 shadow-soft">
                    <h3 class="text-sm font-semibold text-ink">كيانات الأدوات المربوطة ({{ $package->teacherTools->count() }})</h3>
                    <ul class="mt-3 space-y-2">
                        @foreach($package->teacherTools as $linkedTool)
                            <li class="flex items-center justify-between gap-3 rounded-xl border border-line bg-surface px-3 py-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-ink">{{ $linkedTool->title_ar }}</p>
                                    <p class="text-xs text-muted">{{ $linkedTool->typeLabel() }}</p>
                                </div>
                                <a href="{{ route('admin.teacher-tools.show', $linkedTool) }}" class="text-xs font-semibold text-accent">إدارة</a>
                            </li>
                        @endforeach
                    </ul>
                </article>
            @endif

            <article class="rounded-2xl border border-accent/20 bg-accent-soft/20 p-5 shadow-soft">
                <h3 class="text-sm font-semibold text-ink">المسارات التعليمية ({{ $package->learningPaths->count() }})</h3>
                <p class="mt-1 text-xs text-muted">هذه هي المسارات التي تُفعَّل للمعلم عند منحه هذه الباقة.</p>
                @if($package->learningPaths->count() > 0)
                    <ul class="mt-4 space-y-2">
                        @foreach($package->learningPaths as $path)
                            <li class="flex items-center justify-between gap-3 rounded-xl border border-line bg-surface px-3 py-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-ink">{{ $path->title_ar }}</p>
                                    <p class="text-xs text-muted">{{ $path->skill_focus_ar ?: $path->slug }}</p>
                                </div>
                                <a href="{{ route('admin.learning-paths.show', $path) }}" class="text-xs font-semibold text-accent">بناء</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-4 py-4 text-center text-sm text-muted">لا مسارات مربوطة — عدّل الباقة واختر المسارات.</p>
                @endif
            </article>

            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-sm font-semibold text-ink">تفعيل الباقة لمعلم (متعلّم مهني)</h3>
                <p class="mt-1 text-xs text-muted mb-3">يسجّل استحقاق الباقة ويفتح المسارات المربوطة. الدفع الإلكتروني للباقات التجارية سيُربَط لاحقًا بنفس الخدمة.</p>
                <form method="POST" action="{{ route('admin.packages.activate-learner', $package) }}" class="flex flex-wrap items-end gap-3">
                    @csrf
                    <div class="min-w-[16rem] flex-1">
                        <label class="mb-1 block text-xs text-muted">المعلم</label>
                        <select name="user_id" required class="h-11 w-full rounded-xl border border-line bg-surface px-3 text-sm">
                            <option value="">اختر معلمًا…</option>
                            @foreach(($learners ?? []) as $learner)
                                <option value="{{ $learner->id }}">{{ $learner->name }} — {{ $learner->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white">
                        تفعيل الباقة
                    </button>
                </form>
            </article>

            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-sm font-semibold text-ink">الكورسات المسجّلة في الباقة ({{ $package->courses->count() }})</h3>
                @if($package->courses->count() > 0)
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach($package->courses as $course)
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-line px-3 py-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-ink">{{ $course->title }}</p>
                                    <p class="mt-0.5 text-xs tabular-nums text-muted">
                                        @if((float) $course->price > 0)
                                            {{ number_format((float) $course->price, 2) }} {{ $package->currencyCode() }}
                                        @else
                                            مجاني
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('admin.advanced-courses.show', $course) }}" class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg border border-line text-muted hover:bg-accent-soft hover:text-accent">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 text-center text-sm text-muted py-6">لا كورسات مسجّلة مربوطة بهذه الباقة</p>
                @endif
            </article>
        </div>
    </div>
</div>
@endsection
