@extends('layouts.instructor-timeline')

@section('title', __('instructor.lessons_breadcrumb') . ' — ' . $course->title)
@section('page_title', __('instructor.lessons_manage_title'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.courses.index') }}">{{ __('instructor.courses') }}</a>
                <span>/</span>
                <a href="{{ route('instructor.courses.show', $course->id) }}">{{ $course->title }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.lessons_breadcrumb') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-book-open su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.lessons_manage_title') }}
            </h1>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.courses.lessons.create', $course->id) }}" class="su-btn su-btn--primary">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.lessons_add') }}
            </a>
            <a href="{{ route('instructor.courses.show', $course->id) }}" class="su-btn">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#b91c1c;font-size:13px">
            {{ session('error') }}
        </div>
    @endif

    <section class="su-card" style="margin-bottom:20px">
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px">
            <div>
                <h2 style="margin:0;font-size:16px;font-weight:600;color:var(--su-ink)">{{ $course->title }}</h2>
                <p style="margin:6px 0 0;font-size:13px;color:var(--su-ink-40)">
                    <i class="fas fa-book-open" aria-hidden="true"></i>
                    {{ __('instructor.lessons_count') }}: <strong style="color:var(--su-ink)">{{ $lessons->total() }}</strong>
                </p>
            </div>
            <a href="{{ route('instructor.courses.curriculum', $course->id) }}" class="su-btn">
                <i class="fas fa-sitemap" aria-hidden="true"></i>
                {{ __('instructor.build_curriculum') }}
            </a>
        </div>
    </section>

    <section class="su-card su-card--flush">
        <div style="padding:14px 16px;border-bottom:0.5px solid var(--su-line)">
            <h3 class="su-card__title" style="margin:0">
                <i class="fas fa-list" aria-hidden="true"></i>
                {{ __('instructor.lessons_list') }}
            </h3>
        </div>

        @if($lessons->count() > 0)
            <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
                <table class="su-table">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>{{ __('instructor.lessons_col_title') }}</th>
                            <th>{{ __('instructor.lessons_col_type') }}</th>
                            <th>{{ __('instructor.lessons_col_duration') }}</th>
                            <th>{{ __('instructor.lessons_col_order') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="lessons-sortable">
                        @foreach($lessons as $lesson)
                            <tr data-lesson-id="{{ $lesson->id }}">
                                <td><i class="fas fa-grip-vertical" style="color:var(--su-ink-40);cursor:move" aria-hidden="true"></i></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        @php
                                            $icoSoft = match($lesson->type) {
                                                'video' => 'su-soft-4',
                                                'text' => 'su-soft-1',
                                                'document' => 'su-soft-2',
                                                default => 'su-soft-3',
                                            };
                                            $ico = match($lesson->type) {
                                                'video' => 'fa-video',
                                                'text' => 'fa-file-alt',
                                                'document' => 'fa-file-pdf',
                                                default => 'fa-question-circle',
                                            };
                                        @endphp
                                        <span class="su-list-item__ico {{ $icoSoft }}" style="width:36px;height:36px"><i class="fas {{ $ico }}" aria-hidden="true"></i></span>
                                        <div>
                                            <strong style="font-weight:600">{{ $lesson->title }}</strong>
                                            @if($lesson->is_free)
                                                <div style="font-size:11px;color:#15803d">{{ __('instructor.free') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="su-chip">
                                        @if($lesson->type === 'video') {{ __('instructor.lessons_type_video') }}
                                        @elseif($lesson->type === 'text') {{ __('instructor.lessons_type_text') }}
                                        @elseif($lesson->type === 'document') {{ __('instructor.lessons_type_document') }}
                                        @else {{ __('instructor.lessons_type_quiz') }}
                                        @endif
                                    </span>
                                </td>
                                <td class="tabular-nums" style="color:var(--su-ink-40)">
                                    @if($lesson->duration_minutes)
                                        {{ __('instructor.lessons_min_short', ['n' => $lesson->duration_minutes]) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td><span class="su-chip">{{ $lesson->order }}</span></td>
                                <td>
                                    <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
                                        <input type="checkbox"
                                               class="toggle-status"
                                               data-lesson-id="{{ $lesson->id }}"
                                               {{ $lesson->is_active ? 'checked' : '' }}>
                                        <span class="{{ $lesson->is_active ? '' : '' }}" style="color:{{ $lesson->is_active ? '#15803d' : 'var(--su-ink-40)' }}">
                                            {{ $lesson->is_active ? __('instructor.active') : __('instructor.inactive') }}
                                        </span>
                                    </label>
                                </td>
                                <td style="text-align:end">
                                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px">
                                        <a href="{{ route('instructor.courses.lessons.show', [$course->id, $lesson->id]) }}" class="su-icon-link" title="{{ __('common.view') }}"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                        <a href="{{ route('instructor.courses.lessons.edit', [$course->id, $lesson->id]) }}" class="su-icon-link su-icon-link--ghost" title="{{ __('common.edit') }}"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                        <button type="button"
                                                class="delete-lesson su-icon-link"
                                                style="background:#fee2e2;color:#b91c1c;border:0;cursor:pointer"
                                                data-lesson-id="{{ $lesson->id }}"
                                                data-lesson-title="{{ $lesson->title }}"
                                                title="{{ __('common.delete') }}">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($lessons->hasPages())
                <div class="su-pager" style="padding:12px">{{ $lessons->links() }}</div>
            @endif
        @else
            <div class="su-empty">
                <i class="fas fa-book-open" aria-hidden="true"></i>
                <p>{{ __('instructor.lessons_empty_title') }}</p>
                <p style="margin-top:4px;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.lessons_empty_hint') }}</p>
                <a href="{{ route('instructor.courses.lessons.create', $course->id) }}" class="su-btn su-btn--primary" style="margin-top:12px">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    {{ __('instructor.lessons_add_first') }}
                </a>
            </div>
        @endif
    </section>
</div>

<div id="deleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4" role="dialog">
    <div class="su-card" style="max-width:28rem;width:100%">
        <h3 class="su-card__title">{{ __('instructor.lessons_confirm_delete_title') }}</h3>
        <p style="margin:8px 0;font-size:13px;color:var(--su-ink-40)">
            {{ __('instructor.lessons_confirm_delete_body') }}
            <strong style="color:var(--su-ink)" id="lesson-title-to-delete"></strong>؟
        </p>
        <p style="margin:0 0 16px;font-size:12px;color:#b91c1c">{{ __('instructor.lessons_confirm_delete_warn') }}</p>
        <div style="display:flex;gap:8px">
            <button type="button" id="delete-modal-cancel" class="su-btn" style="flex:1;justify-content:center">{{ __('instructor.cancel') }}</button>
            <form id="delete-form" method="POST" style="flex:1">
                @csrf
                @method('DELETE')
                <button type="submit" class="su-btn su-btn--danger" style="width:100%;justify-content:center">{{ __('common.delete') }}</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    document.querySelectorAll('.toggle-status').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const lessonId = this.dataset.lessonId;
            const url = '/instructor/courses/{{ $course->id }}/lessons/' + lessonId + '/toggle-status';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const label = this.nextElementSibling;
                    label.textContent = data.is_active ? @json(__('instructor.active')) : @json(__('instructor.inactive'));
                    label.style.color = data.is_active ? '#15803d' : 'var(--su-ink-40)';
                }
            })
            .catch(() => {
                this.checked = !this.checked;
                alert('حدث خطأ أثناء تحديث الحالة');
            });
        });
    });

    const modal = document.getElementById('deleteModal');
    const cancelBtn = document.getElementById('delete-modal-cancel');

    document.querySelectorAll('.delete-lesson').forEach(btn => {
        btn.addEventListener('click', function() {
            const lessonId = this.dataset.lessonId;
            const lessonTitle = this.dataset.lessonTitle;
            document.getElementById('lesson-title-to-delete').textContent = lessonTitle;
            document.getElementById('delete-form').action = '/instructor/courses/{{ $course->id }}/lessons/' + lessonId;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    function closeDeleteModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    if (cancelBtn) cancelBtn.addEventListener('click', closeDeleteModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeDeleteModal();
    });
});
</script>
@endpush
@endsection
