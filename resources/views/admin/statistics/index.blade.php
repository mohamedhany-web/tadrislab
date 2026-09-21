@extends('layouts.admin')

@section('title', 'لوحة المؤشرات الرئيسية')
@section('page_title', 'لوحة المؤشرات الرئيسية')

@section('content')
@php
    $primaryCards = [
        [
            'label' => 'إجمالي المستخدمين',
            'value' => number_format($totalUsers),
            'icon' => 'fas fa-users',
            'footer' => "+$newUsersThisMonth مستخدم جديد هذا الشهر",
        ],
        [
            'label' => 'الطلاب النشطون',
            'value' => number_format($totalStudents),
            'icon' => 'fas fa-user-graduate',
            'footer' => round(($totalStudents / max($totalUsers, 1)) * 100, 1) . '% من إجمالي المستخدمين',
        ],
        [
            'label' => 'المدربون',
            'value' => number_format($totalTeachers),
            'icon' => 'fas fa-chalkboard-teacher',
            'footer' => round(($totalTeachers / max($totalUsers, 1)) * 100, 1) . '% من إجمالي المستخدمين',
        ],
        [
            'label' => 'الكورسات النشطة',
            'value' => number_format($totalCourses),
            'icon' => 'fas fa-code',
            'footer' => number_format($totalEnrollments) . ' تسجيل إجمالي',
        ],
    ];

    $secondaryCards = [
        [
            'label' => 'مسارات التعلم',
            'value' => number_format($totalAcademicYears),
            'icon' => 'fas fa-route',
        ],
        [
            'label' => 'مجموعات المهارات',
            'value' => number_format($totalSubjects),
            'icon' => 'fas fa-layer-group',
        ],
        [
            'label' => 'تسجيلات هذا الشهر',
            'value' => number_format($newEnrollmentsThisMonth),
            'icon' => 'fas fa-user-plus',
        ],
    ];

    $activityLogRoute = \Illuminate\Support\Facades\Route::has('admin.activity-log')
        ? route('admin.activity-log')
        : null;
@endphp

<div class="space-y-5">
    <section class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-col gap-4 border-b border-line px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="flex items-center gap-4">
                <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-chart-bar text-sm"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-semibold tracking-tight text-ink md:text-[28px]">لوحة المؤشرات الرئيسية</h2>
                    <p class="mt-1 text-sm text-muted">عرض سريع لحالة المنصة ونموها عبر المستخدمين والمحتوى والتسجيلات.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.statistics.users') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent">
                    <i class="fas fa-users text-xs"></i>
                    إحصائيات المستخدمين
                </a>
                <a href="{{ route('admin.statistics.courses') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-code text-xs"></i>
                    إحصائيات الكورسات
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 sm:p-5 xl:grid-cols-4">
            @foreach ($primaryCards as $card)
                <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-muted">{{ $card['label'] }}</p>
                            <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-ink">{{ $card['value'] }}</p>
                        </div>
                        <div class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                            <i class="{{ $card['icon'] }} text-sm"></i>
                        </div>
                    </div>
                    <p class="mt-2 border-t border-line pt-2 text-[11px] text-muted">{{ htmlspecialchars($card['footer'], ENT_QUOTES, 'UTF-8') }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">تفاصيل إضافية</h3>
            <p class="mt-0.5 text-xs text-muted">توزيع المسارات التعليمية وشدة النشاط خلال الشهر الحالي.</p>
        </div>
        <div class="grid grid-cols-1 gap-3 p-4 md:grid-cols-3 sm:p-5">
            @foreach ($secondaryCards as $card)
                <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-muted">{{ $card['label'] }}</p>
                            <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink">{{ $card['value'] }}</p>
                        </div>
                        <div class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                            <i class="{{ $card['icon'] }} text-sm"></i>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">أكثر الكورسات تسجيلاً</h3>
                <p class="mt-0.5 text-xs text-muted">أكثر المسارات جذباً للطلاب خلال الفترة الحالية.</p>
            </div>
            <div class="p-4 sm:p-5">
                @if ($popularCourses->count() > 0)
                    <div class="space-y-3">
                        @foreach ($popularCourses as $course)
                            <div class="rounded-xl border border-line bg-surface p-4 transition hover:border-accent/30 hover:shadow-soft">
                                <div class="mb-2 flex items-center justify-between gap-2">
                                    <h4 class="text-sm font-semibold text-ink">{{ htmlspecialchars($course->title, ENT_QUOTES, 'UTF-8') }}</h4>
                                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-accent-soft px-3 py-1 text-xs font-semibold text-accent">
                                        <i class="fas fa-user-friends"></i>
                                        {{ number_format($course->enrollments_count) }} معلم
                                    </span>
                                </div>
                                <p class="text-xs text-muted">
                                    {{ htmlspecialchars($course->academicYear->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }} •
                                    {{ htmlspecialchars($course->academicSubject->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-line bg-canvas p-8 text-center">
                        <div class="mx-auto mb-3 inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                            <i class="fas fa-chart-line text-sm"></i>
                        </div>
                        <p class="text-sm font-semibold text-ink">لا توجد بيانات</p>
                        <p class="mt-1 text-xs text-muted">لا توجد بيانات متاحة حالياً.</p>
                    </div>
                @endif
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">آخر النشاطات</h3>
                <p class="mt-0.5 text-xs text-muted">تحركات الفريق خلال الساعات القليلة الماضية.</p>
            </div>
            <div class="p-4 sm:p-5">
                @if ($recentActivities->count() > 0)
                    <div class="space-y-3">
                        @foreach ($recentActivities as $activity)
                            <div class="flex items-start gap-3 rounded-xl border border-line bg-surface p-4 transition hover:shadow-soft">
                                <div class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-ink">
                                        <span class="font-semibold">{{ htmlspecialchars($activity->user->name ?? 'مستخدم مجهول', ENT_QUOTES, 'UTF-8') }}</span>
                                        {{ htmlspecialchars($activity->description ?? 'نشاط', ENT_QUOTES, 'UTF-8') }}
                                    </p>
                                    <p class="mt-1 text-xs text-muted">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if ($activityLogRoute)
                        <div class="mt-5 border-t border-line pt-5 text-center">
                            <a href="{{ $activityLogRoute }}" class="btn-press inline-flex items-center gap-2 text-sm font-semibold text-accent hover:text-[#184888]">
                                <i class="fas fa-list"></i>
                                استعرض كامل السجل
                                <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="rounded-xl border border-line bg-canvas p-8 text-center">
                        <div class="mx-auto mb-3 inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                            <i class="fas fa-bell-slash text-sm"></i>
                        </div>
                        <p class="text-sm font-semibold text-ink">لا توجد نشاطات</p>
                        <p class="mt-1 text-xs text-muted">لا توجد نشاطات حديثة.</p>
                    </div>
                @endif
            </div>
        </article>
    </section>
</div>
@endsection
