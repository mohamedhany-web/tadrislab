@extends('layouts.instructor-timeline')

@section('title', __('instructor.my_requests_to_management') . ' - ' . config('app.name'))
@section('page_title', __('instructor.submit_requests_to_management'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-inbox su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.my_requests_to_management') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.my_requests_description') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.management-requests.create') }}" class="su-btn su-btn--primary">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.new_request') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            {{ session('success') }}
        </div>
    @endif

    <section class="su-card" style="margin-bottom:16px">
        <form method="GET" class="su-form-grid" style="grid-template-columns:1fr auto">
            <div class="su-field">
                <label for="status">{{ __('common.status') }}</label>
                <select name="status" id="status" class="su-select">
                    <option value="">{{ __('instructor.all_statuses_filter') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('instructor.pending_review') }}</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('instructor.approved') }}</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('instructor.rejected') }}</option>
                </select>
            </div>
            <div class="su-form-actions" style="align-items:flex-end">
                <button type="submit" class="su-btn su-btn--primary" style="height:40px">{{ __('common.search') }}</button>
            </div>
        </form>
    </section>

    <section class="su-card su-card--flush">
        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
            <table class="su-table">
                <thead>
                    <tr>
                        <th>{{ __('instructor.request_subject') }}</th>
                        <th>{{ __('common.status') }}</th>
                        <th>{{ __('common.date') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        @php
                            $chip = match ($req->status) {
                                'pending' => 'su-chip--warn',
                                'approved' => 'su-chip--ok',
                                default => 'su-chip--off',
                            };
                            $label = match ($req->status) {
                                'pending' => __('instructor.pending_review'),
                                'approved' => __('instructor.approved'),
                                default => __('instructor.rejected'),
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong style="font-weight:600">{{ $req->subject }}</strong>
                                <div style="font-size:12px;color:var(--su-ink-40);margin-top:2px">{{ Str::limit($req->message, 60) }}</div>
                            </td>
                            <td><span class="su-chip {{ $chip }}">{{ $label }}</span></td>
                            <td class="tabular-nums" style="color:var(--su-ink-40)">{{ $req->created_at->format('Y-m-d H:i') }}</td>
                            <td style="text-align:end">
                                <a href="{{ route('instructor.management-requests.show', $req) }}" class="su-btn" style="height:32px">
                                    {{ __('common.view') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="su-empty">
                                    <i class="fas fa-inbox" aria-hidden="true"></i>
                                    <p>{{ __('instructor.no_requests_yet') }}</p>
                                    <a href="{{ route('instructor.management-requests.create') }}" class="su-btn su-btn--primary" style="margin-top:8px">
                                        {{ __('instructor.new_request') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="su-pager" style="padding:12px">{{ $requests->appends(request()->query())->links() }}</div>
        @endif
    </section>
</div>
@endsection
