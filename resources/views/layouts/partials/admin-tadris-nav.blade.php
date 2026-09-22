{{-- Brief V3 primary admin nav — hubs + groups from config/admin_nav.php --}}
@php
    $tadrisNavHubs = \App\Support\AdminNav::hubsFor($u);
@endphp
@foreach($tadrisNavHubs as $hub)
<li class="sidebar-section-label">{{ $hub['label'] }}</li>
@foreach($hub['sections'] as $section)
@php $sectionOpen = ! empty($section['open']); @endphp
<li x-data="{ open: {{ $sectionOpen ? 'true' : 'false' }} }">
    <button type="button" @click="open = !open" class="sidebar-group-btn {{ $sectionOpen ? 'active' : '' }}">
        <span class="flex items-center gap-3"><i class="{{ $section['icon'] }}"></i><span>{{ $section['label'] }}</span></span>
        <i class="fas fa-chevron-down chevron" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <ul x-show="open" x-cloak class="mt-1 mr-3 space-y-0.5 border-r border-white/10 pr-3">
        @if(! empty($section['note']))
            <li class="px-2 pb-1 text-[10px] leading-snug text-white/45">{{ $section['note'] }}</li>
        @endif
        @foreach($section['items'] as $item)
            <li>
                <a href="{{ $item['url'] }}" class="sidebar-sub-link {{ $item['active'] ? 'active' : '' }}">
                    <span>{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</li>
@endforeach
@endforeach
