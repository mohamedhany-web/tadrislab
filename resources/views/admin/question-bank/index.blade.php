@extends('layouts.admin')

@section('title', 'بنك الأسئلة - ' . config('app.name'))
@section('page_title', 'بنك الأسئلة')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحتوى · الامتحانات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">بنك الأسئلة</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">إدارة وتنظيم الأسئلة للامتحانات.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.question-categories.index') }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-tags text-xs"></i>
                إدارة التصنيفات
            </a>
            <a href="{{ route('admin.question-bank.create') }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-plus text-xs"></i>
                إضافة سؤال جديد
            </a>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-soft">
            <i class="fas fa-check-circle ml-1"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 shadow-soft">
            <i class="fas fa-exclamation-circle ml-1"></i> {{ session('error') }}
        </div>
    @endif

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-question-circle text-sm"></i></div>
            <p class="mt-3 text-xs font-medium text-muted">إجمالي الأسئلة</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-ink">{{ $stats['total_questions'] }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-check text-sm"></i></div>
            <p class="mt-3 text-xs font-medium text-muted">أسئلة نشطة</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-emerald-700">{{ $stats['active_questions'] }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-folder text-sm"></i></div>
            <p class="mt-3 text-xs font-medium text-muted">تصنيفات</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-ink">{{ $stats['categories_count'] }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-layer-group text-sm"></i></div>
            <p class="mt-3 text-xs font-medium text-muted">أنواع أسئلة</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-ink">{{ count($stats['by_type']) }}</p>
        </article>
    </section>

    <form method="GET" class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <div>
                <label for="search" class="{{ $labelClass }}">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="نص السؤال..."
                       class="{{ $fieldClass }}">
            </div>
            <div>
                <label for="category_id" class="{{ $labelClass }}">التصنيف</label>
                <select name="category_id" id="category_id" class="{{ $fieldClass }}">
                    <option value="">جميع التصنيفات</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->full_path ?? $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="type" class="{{ $labelClass }}">نوع السؤال</label>
                <select name="type" id="type" class="{{ $fieldClass }}">
                    <option value="">جميع الأنواع</option>
                    @foreach($questionTypes as $key => $type)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="difficulty" class="{{ $labelClass }}">مستوى الصعوبة</label>
                <select name="difficulty" id="difficulty" class="{{ $fieldClass }}">
                    <option value="">جميع المستويات</option>
                    @foreach($difficultyLevels as $key => $level)
                        <option value="{{ $key }}" {{ request('difficulty') == $key ? 'selected' : '' }}>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-press inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-search text-xs"></i> بحث
                </button>
                @if(request()->hasAny(['search', 'category_id', 'type', 'difficulty']))
                    <a href="{{ route('admin.question-bank.index') }}"
                       class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-line text-muted transition hover:bg-accent-soft hover:text-accent"
                       title="إعادة تعيين">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>

    @if($questions->count() > 0)
        <p class="text-xs text-muted">
            عرض <span class="font-semibold tabular-nums text-ink">{{ $questions->firstItem() }}</span>–<span class="font-semibold tabular-nums text-ink">{{ $questions->lastItem() }}</span>
            من <span class="font-semibold tabular-nums text-ink">{{ $questions->total() }}</span>
        </p>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-3">
                <h3 class="text-sm font-semibold text-ink">الأسئلة ({{ $questions->total() }})</h3>
            </div>
            <div class="divide-y divide-line">
                @foreach($questions as $question)
                    @php
                        $typeClass = match($question->type) {
                            'multiple_choice' => 'bg-accent-soft text-accent',
                            'true_false' => 'bg-emerald-50 text-emerald-700',
                            'fill_blank' => 'bg-amber-50 text-amber-800',
                            'essay' => 'bg-[#f2f5f4] text-muted',
                            default => 'bg-[#f2f5f4] text-muted',
                        };
                        $difficultyClass = match($question->difficulty_level) {
                            'easy' => 'bg-emerald-50 text-emerald-700',
                            'medium' => 'bg-amber-50 text-amber-800',
                            default => 'bg-rose-50 text-rose-700',
                        };
                    @endphp
                    <div class="p-4 hover:bg-[#f8faf9] sm:p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="mb-2 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $typeClass }}">{{ $question->type_text }}</span>
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $difficultyClass }}">{{ $question->difficulty_text }}</span>
                                    <span class="text-xs text-muted">{{ $question->points }} نقطة</span>
                                    @if($question->hasMedia())
                                        <span class="inline-flex items-center rounded-full bg-accent-soft px-2 py-0.5 text-[11px] font-medium text-accent">
                                            <i class="fas fa-paperclip ml-1 text-[9px]"></i> وسائط
                                        </span>
                                    @endif
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $question->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $question->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </div>
                                <h4 class="mb-2 text-sm font-semibold text-ink">{{ Str::limit(strip_tags($question->question), 120) }}</h4>
                                <div class="flex flex-wrap items-center gap-4 text-xs text-muted">
                                    @if($question->category)
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-tag"></i>
                                            {{ $question->category->full_path ?? $question->category->name ?? '—' }}
                                        </span>
                                    @endif
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-clock"></i>
                                        {{ $question->created_at->diffForHumans() }}
                                    </span>
                                    @if($question->tags && is_array($question->tags))
                                        <div class="flex flex-wrap items-center gap-1">
                                            @foreach(array_slice($question->tags, 0, 3) as $tag)
                                                <span class="rounded bg-[#f2f5f4] px-2 py-0.5 text-[11px]">{{ is_string($tag) ? $tag : '' }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-1.5">
                                <a href="{{ route('admin.question-bank.show', $question) }}" class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent hover:bg-accent-soft" title="عرض"><i class="fas fa-eye text-xs"></i></a>
                                <a href="{{ route('admin.question-bank.edit', $question) }}" class="inline-flex size-9 items-center justify-center rounded-xl border border-line text-muted hover:bg-accent-soft hover:text-accent" title="تعديل"><i class="fas fa-edit text-xs"></i></a>
                                <form action="{{ route('admin.question-bank.duplicate', $question) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex size-9 items-center justify-center rounded-xl border border-line text-emerald-700 hover:bg-emerald-50" title="نسخ"><i class="fas fa-copy text-xs"></i></button>
                                </form>
                                <form action="{{ route('admin.question-bank.destroy', $question) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex size-9 items-center justify-center rounded-xl border border-line text-rose-600 hover:bg-rose-50" title="حذف"><i class="fas fa-trash text-xs"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-line px-4 py-3">
                {{ $questions->links() }}
            </div>
        </article>
    @else
        <article class="rounded-2xl border border-dashed border-line bg-surface px-6 py-14 text-center shadow-soft">
            <div class="mx-auto inline-flex size-14 items-center justify-center rounded-2xl bg-[#f2f5f4] text-accent">
                <i class="fas fa-question-circle text-xl"></i>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-ink">لا توجد أسئلة</h3>
            <p class="mt-1 text-sm text-muted">ابدأ ببناء بنك الأسئلة أو أنشئ تصنيفات أولاً.</p>
            <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('admin.question-categories.index') }}"
                   class="btn-press inline-flex h-10 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent">
                    <i class="fas fa-tags text-xs"></i> التصنيفات
                </a>
                <a href="{{ route('admin.question-bank.create') }}"
                   class="btn-press inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-plus text-xs"></i> إضافة أول سؤال
                </a>
            </div>
        </article>
    @endif

    @if(!empty($stats['by_type']))
        <article class="rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-3">
                <h3 class="text-sm font-semibold text-ink">توزيع الأسئلة حسب النوع</h3>
            </div>
            <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                @foreach($stats['by_type'] as $type => $count)
                    <div class="rounded-xl border border-line bg-[#f8faf9] p-4 text-center">
                        <div class="text-2xl font-semibold tabular-nums text-ink">{{ $count }}</div>
                        <div class="mt-0.5 text-xs text-muted">{{ $questionTypes[$type] ?? $type }}</div>
                    </div>
                @endforeach
            </div>
        </article>
    @endif
</div>
@endsection
