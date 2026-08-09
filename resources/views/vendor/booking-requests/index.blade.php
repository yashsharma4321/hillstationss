@extends('layouts.vendor')

@section('header', 'Booking Requests')

@section('styles')
<style>
    .filter-bar { display:flex; flex-wrap:wrap; gap:1rem; align-items:flex-end; margin-bottom:1.5rem; }
    .filter-group { display:flex; flex-direction:column; gap:0.35rem; }
    .filter-group label { font-size:0.72rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; }
    .filter-group select { padding:0.55rem 0.9rem; border:1px solid var(--border); border-radius:0.5rem; font-size:0.875rem; background:white; }
    .card-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:1.25rem; }
    .req-card { background:white; border:1px solid var(--border); border-radius:0.75rem; padding:1.25rem 1.5rem; box-shadow:0 1px 4px rgba(0,0,0,0.04); }
    .req-card-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem; }
    .req-name { font-weight:700; font-size:0.95rem; color:#1e293b; }
    .req-prop { font-size:0.78rem; color:var(--text-muted); margin-top:0.15rem; }
    .req-row { display:flex; gap:0.5rem; align-items:center; font-size:0.82rem; color:#475569; margin-bottom:0.35rem; }
    .req-row svg { flex-shrink:0; }
    .badge { display:inline-flex; padding:0.2rem 0.65rem; border-radius:999px; font-size:0.72rem; font-weight:700; }
    .badge-pending   { background:#fef3c7; color:#92400e; }
    .badge-contacted { background:#dbeafe; color:#1d4ed8; }
    .badge-converted { background:#dcfce7; color:#15803d; }
    .badge-rejected  { background:#fee2e2; color:#dc2626; }
    .req-msg { background:#f8fafc; border-radius:0.4rem; padding:0.6rem 0.75rem; font-size:0.8rem; color:#64748b; margin-top:0.75rem; }
    .status-form select { padding:0.3rem 0.55rem; border:1px solid var(--border); border-radius:0.4rem; font-size:0.78rem; cursor:pointer; background:white; }
</style>
@endsection

@section('content')

@if(session('success'))
    <div style="background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:0.875rem 1.25rem; border-radius:0.5rem; margin-bottom:1.5rem;">
        {{ session('success') }}
    </div>
@endif

<div class="filter-bar">
    <form method="GET" action="{{ route('vendor.booking-requests.index') }}" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:flex-end;">
        <div class="filter-group">
            <label>Property</label>
            <select name="property_id">
                <option value="">All Properties</option>
                @foreach($properties as $prop)
                    <option value="{{ $prop->id }}" {{ request('property_id') == $prop->id ? 'selected' : '' }}>{{ $prop->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                @foreach(['pending','contacted','converted','rejected'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" style="padding:0.55rem 1.25rem; background:var(--primary); color:white; border:none; border-radius:0.5rem; font-weight:600; cursor:pointer;">Filter</button>
        <a href="{{ route('vendor.booking-requests.index') }}" style="padding:0.55rem 1rem; background:#f1f5f9; color:var(--text-main); border-radius:0.5rem; font-size:0.875rem; text-decoration:none; font-weight:500;">Clear</a>
    </form>
    <div style="margin-left:auto; font-size:0.85rem; color:var(--text-muted); align-self:center;">
        {{ $requests->total() }} requests
    </div>
</div>

@if($requests->isEmpty())
    <div style="text-align:center; padding:4rem 2rem; background:white; border:1px solid var(--border); border-radius:0.75rem;">
        <svg width="48" height="48" fill="none" stroke="#a5b4fc" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem; display:block;"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
        <h3 style="font-size:1.1rem; font-weight:700; color:var(--text-main); margin-bottom:0.5rem;">No booking requests yet</h3>
        <p style="color:var(--text-muted);">When guests enquire about your properties, they'll appear here.</p>
    </div>
@else
    <div class="card-grid">
        @foreach($requests as $req)
            <div class="req-card">
                <div class="req-card-header">
                    <div>
                        <div class="req-name">{{ $req->name }}</div>
                        <div class="req-prop">{{ $req->property->name ?? '—' }}</div>
                    </div>
                    <span class="badge badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span>
                </div>

                <div class="req-row">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    {{ $req->email }}
                </div>
                @if($req->phone)
                <div class="req-row">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.22 1.18 2 2 0 012.22.01h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.16 6.16l1.07-1.07a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                    {{ $req->phone }}
                </div>
                @endif
                <div class="req-row">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ $req->check_in->format('d M Y') }} → {{ $req->check_out->format('d M Y') }}
                </div>
                <div class="req-row">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    {{ $req->adults }} adult{{ $req->adults > 1 ? 's' : '' }}
                </div>

                @if($req->message)
                    <div class="req-msg">{{ $req->message }}</div>
                @endif

                <div style="margin-top:1rem; display:flex; align-items:center; justify-content:space-between;">
                    <span style="font-size:0.75rem; color:var(--text-muted);">{{ $req->created_at->format('d M Y, h:i A') }}</span>
                    <form method="POST" action="{{ route('vendor.booking-requests.status', $req) }}">
                        @csrf @method('PATCH')
                        <select name="status" class="status-form" onchange="this.form.submit()">
                            @foreach(['pending','contacted','converted','rejected'] as $s)
                                <option value="{{ $s }}" {{ $req->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top:1.5rem;">
        {{ $requests->links() }}
    </div>
@endif

@endsection
