@extends('layouts.instructor-timeline')

@section('title', __('instructor.o1a_title'))
@section('page_title', __('instructor.o1a_title'))

@section('content')
@php
    $windowsCount = $rules->count();
    $daysWithSlots = $grouped->filter(fn ($g) => $g['rules']->isNotEmpty())->count();
    $existingSlots = $rules->map(function ($r) {
        return [
            'day_of_week' => (string) $r->day_of_week,
            'start_time' => substr((string) $r->start_time, 0, 5),
            'end_time' => substr((string) $r->end_time, 0, 5),
            'slot_duration_minutes' => (string) ($r->slot_duration_minutes ?: 50),
        ];
    })->values();
@endphp

<div class="su-page" x-data="availabilityForm()">
    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            <i class="fas fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#b91c1c;font-size:13px">
            <ul style="margin:0;padding-inline-start:18px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="su-page-head">
        <div class="min-w-0">
            <div class="su-chip-row" style="margin-bottom:8px">
                <span class="su-chip su-soft-1">
                    <i class="fas fa-user-graduate" aria-hidden="true"></i>
                    {{ __('instructor.o1a_chip') }}
                </span>
            </div>
            <h1 class="su-page-head__title">
                <i class="fas fa-calendar-check su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.o1a_title') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.o1a_subtitle') }}</p>
        </div>
        <div class="su-page-head__actions">
            @if(Route::has('instructor.tutor-work-schedule.index'))
                <a href="{{ route('instructor.tutor-work-schedule.index') }}" class="su-btn">
                    <i class="fas fa-users" aria-hidden="true"></i>
                    {{ __('instructor.o1a_group_schedule') }}
                </a>
            @endif
            @if(Route::has('instructor.one-to-one-sessions.index'))
                <a href="{{ route('instructor.one-to-one-sessions.index') }}" class="su-btn su-btn--primary">
                    <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
                    {{ __('instructor.o1o_title') }}
                </a>
            @endif
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.o1a_windows') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v" x-text="slots.length">{{ $windowsCount }}</div>
                <div class="su-kpi__d"><i class="fas fa-window-maximize" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.o1a_active_days') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($daysWithSlots) }}</div>
                <div class="su-kpi__d"><i class="fas fa-calendar-day" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.o1a_saved') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($windowsCount) }}</div>
                <div class="su-kpi__d"><i class="fas fa-save" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.tws_hint_label') }}</div>
            <div class="su-kpi__row">
                <div style="font-size:12px;line-height:1.4;color:var(--su-ink-40);padding-top:4px">{{ __('instructor.o1a_hint') }}</div>
            </div>
        </div>
    </section>

    <div class="su-page-grid">
        <form method="POST" action="{{ route('instructor.one-to-one-availability.update') }}" class="su-card" style="display:flex;flex-direction:column;gap:16px">
            @csrf
            @include('partials.timezone-select', [
                'value' => old('timezone', auth()->user()?->timezoneCode()),
                'class' => 'su-select',
                'labelClass' => 'block text-[12px] font-medium mb-1.5',
                'label' => __('instructor.o1a_timezone'),
            ])

            <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:12px">
                <div class="min-w-0">
                    <h2 class="su-card__title" style="margin:0">{{ __('instructor.o1a_edit_windows') }}</h2>
                    <p style="margin:4px 0 0;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.o1a_edit_hint') }}</p>
                </div>
                <button type="button" @click="addSlot()" class="su-btn su-btn--primary" style="height:36px">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    {{ __('instructor.o1a_add_slot') }}
                </button>
            </div>

            <div style="display:flex;flex-direction:column;gap:12px">
                <template x-for="(slot, index) in slots" :key="slot._uid">
                    <div class="su-card su-soft-1" style="padding:14px">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:12px">
                            <span class="su-chip su-soft-2">
                                {{ __('instructor.o1a_slot') }} <span x-text="index + 1"></span>
                            </span>
                            <button type="button" @click="removeSlot(index)" x-show="slots.length > 1" class="su-btn" style="height:32px;color:#b91c1c">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                                {{ __('instructor.o1a_remove_slot') }}
                            </button>
                        </div>
                        <div class="su-form-grid" style="grid-template-columns:repeat(2,minmax(0,1fr));align-items:start">
                            <div class="su-field" style="grid-column:span 2">
                                <label>{{ __('instructor.o1a_day') }}</label>
                                <select :name="'slots['+index+'][day_of_week]'" x-model="slot.day_of_week" class="su-select" required>
                                    @foreach($dayLabels as $day => $label)
                                        <option value="{{ $day }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="su-field">
                                <label>{{ __('instructor.o1a_from') }}</label>
                                <input type="time" step="60" :name="'slots['+index+'][start_time]'" x-model="slot.start_time" class="su-input" required>
                            </div>
                            <div class="su-field">
                                <label>{{ __('instructor.o1a_to') }}</label>
                                <input type="time" step="60" :name="'slots['+index+'][end_time]'" x-model="slot.end_time" class="su-input" required>
                            </div>
                            <div class="su-field" style="grid-column:span 2;max-width:220px">
                                <label>{{ __('instructor.o1o_minutes') }}</label>
                                <input type="number" :name="'slots['+index+'][slot_duration_minutes]'" x-model="slot.slot_duration_minutes" min="30" max="180" step="15" class="su-input">
                            </div>
                        </div>
                        <p style="margin:12px 0 0;font-size:11px;font-weight:600" :style="slotYield(slot) > 0 ? 'color:var(--su-ink)' : 'color:#b91c1c'">
                            <span x-show="slotYield(slot) > 0" x-text="yieldLabel(slotYield(slot))"></span>
                            <span x-show="slotYield(slot) < 1">{{ __('instructor.o1a_yield_zero') }}</span>
                        </p>
                    </div>
                </template>
            </div>

            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;padding-top:12px;border-top:0.5px solid var(--su-line)">
                <p style="margin:0;font-size:11px;color:var(--su-ink-40)">
                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                    {{ __('instructor.o1a_save_hint') }}
                </p>
                <button type="submit" class="su-btn su-btn--primary">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    {{ __('instructor.o1a_save') }}
                </button>
            </div>
        </form>

        <aside>
            <div class="su-card">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:16px">
                    <h2 class="su-card__title" style="margin:0">{{ __('instructor.o1a_current_schedule') }}</h2>
                    <span class="su-chip su-soft-1">{{ __('instructor.o1a_saved_chip') }}</span>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px">
                    @foreach($grouped as $dayGroup)
                        @php $has = $dayGroup['rules']->isNotEmpty(); @endphp
                        <div class="su-card" style="padding:0;overflow:hidden{{ $has ? ';border-color:rgba(11,61,145,.25)' : '' }}">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding:10px 12px;border-bottom:0.5px solid var(--su-line)">
                                <span style="font-size:13px;font-weight:600;color:var(--su-ink)">{{ $dayGroup['label'] }}</span>
                                @if($has)
                                    <span class="su-chip su-soft-1 tabular-nums">{{ $dayGroup['rules']->count() }}</span>
                                @else
                                    <span style="font-size:10px;color:var(--su-ink-40)">{{ __('instructor.o1a_empty_day') }}</span>
                                @endif
                            </div>
                            <div style="padding:10px;display:flex;flex-wrap:wrap;gap:6px;min-height:48px;align-content:flex-start">
                                @forelse($dayGroup['rules'] as $rule)
                                    <span class="su-chip su-soft-1">
                                        <i class="far fa-clock" aria-hidden="true"></i>
                                        {{ substr((string) $rule->start_time, 0, 5) }}–{{ substr((string) $rule->end_time, 0, 5) }}
                                        · {{ (int) $rule->slot_duration_minutes }} {{ __('instructor.o1o_minutes') }}
                                    </span>
                                @empty
                                    <span class="su-chip" style="width:100%;justify-content:center;padding:12px">{{ __('instructor.o1a_no_windows') }}</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
function availabilityForm() {
    const existing = @json($existingSlots);
    const yieldTpl = @json(__('instructor.o1a_yield'));
    let uid = 1;
    const withIds = (existing.length ? existing : [{ day_of_week: '1', start_time: '16:00', end_time: '22:00', slot_duration_minutes: '50' }])
        .map(function (slot) {
            slot._uid = uid++;
            return slot;
        });
    return {
        slots: withIds,
        nextUid: uid,
        addSlot() {
            this.slots.push({
                _uid: this.nextUid++,
                day_of_week: '1',
                start_time: '16:00',
                end_time: '22:00',
                slot_duration_minutes: '50'
            });
        },
        removeSlot(i) {
            this.slots.splice(i, 1);
        },
        minutes(t) {
            if (!t) return 0;
            const p = String(t).split(':');
            return (parseInt(p[0], 10) || 0) * 60 + (parseInt(p[1], 10) || 0);
        },
        slotYield(slot) {
            let start = this.minutes(slot.start_time);
            let end = this.minutes(slot.end_time);
            if (end === 0 && start > 0) end = 24 * 60;
            const duration = parseInt(slot.slot_duration_minutes, 10) || 50;
            if (end <= start || duration < 1) return 0;
            return Math.floor((end - start) / duration);
        },
        yieldLabel(n) {
            return String(yieldTpl).replace(':count', n);
        }
    };
}
</script>
@endsection
