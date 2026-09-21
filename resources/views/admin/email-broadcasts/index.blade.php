@extends('layouts.admin')

@section('title', 'إشعارات البريد — '.$audienceLabel)
@section('page_title', 'إشعارات البريد (Gmail) — '.$audienceLabel)

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">التواصل · إشعارات البريد</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إشعارات البريد (Gmail)</h2>
            <p class="mt-1 text-sm text-muted">الجمهور الحالي: {{ $audienceLabel }}</p>
        </div>
        <a href="{{ route('admin.email-broadcasts.create', $audience) }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
            <i class="fas fa-paper-plane text-xs"></i>
            إرسال بريد جديد
        </a>
    </section>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.email-broadcasts.index', 'all_users') }}"
           class="btn-press inline-flex h-9 items-center rounded-xl border px-3 text-sm font-medium transition {{ $audience === 'all_users' ? 'border-accent bg-accent text-white' : 'border-line bg-surface text-ink hover:bg-accent-soft hover:text-accent' }}">
            كل المستخدمين
        </a>
        <a href="{{ route('admin.email-broadcasts.index', 'students') }}"
           class="btn-press inline-flex h-9 items-center rounded-xl border px-3 text-sm font-medium transition {{ $audience === 'students' ? 'border-accent bg-accent text-white' : 'border-line bg-surface text-ink hover:bg-accent-soft hover:text-accent' }}">
            الطلاب
        </a>
        <a href="{{ route('admin.email-broadcasts.index', 'instructors') }}"
           class="btn-press inline-flex h-9 items-center rounded-xl border px-3 text-sm font-medium transition {{ $audience === 'instructors' ? 'border-accent bg-accent text-white' : 'border-line bg-surface text-ink hover:bg-accent-soft hover:text-accent' }}">
            المدربين
        </a>
        <a href="{{ route('admin.email-broadcasts.index', 'employees') }}"
           class="btn-press inline-flex h-9 items-center rounded-xl border px-3 text-sm font-medium transition {{ $audience === 'employees' ? 'border-accent bg-accent text-white' : 'border-line bg-surface text-ink hover:bg-accent-soft hover:text-accent' }}">
            الموظفين
        </a>
    </div>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">سجل الرسائل</h3>
            <p class="mt-0.5 text-xs text-muted">جميع رسائل البريد المرسلة لهذا الجمهور</p>
        </div>
        <div class="admin-table-wrap overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-line bg-canvas text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium">العنوان</th>
                        <th class="px-4 py-3 text-start font-medium">الحالة</th>
                        <th class="px-4 py-3 text-start font-medium">الإرسال</th>
                        <th class="px-4 py-3 text-start font-medium">المنشئ</th>
                        <th class="px-4 py-3 text-start font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($broadcasts as $b)
                        <tr class="hover:bg-canvas/40">
                            <td class="px-4 py-3 font-medium text-ink">{{ $b->subject }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">{{ $b->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-muted">{{ $b->sent_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-ink-soft">{{ $b->creator?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.email-broadcasts.show', [$audience, $b]) }}"
                                   class="btn-press inline-flex h-8 items-center rounded-lg border border-line px-3 text-xs font-medium text-ink hover:bg-accent-soft hover:text-accent">
                                    تفاصيل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-muted">لا توجد رسائل بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($broadcasts->hasPages())
            <div class="border-t border-line px-4 py-3 sm:px-5">{{ $broadcasts->links() }}</div>
        @endif
    </article>
</div>
@endsection
