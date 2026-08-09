@extends('layouts.admin')

@section('header', 'Booking Requests')

@section('styles')
<style>
    .filter-bar { display:flex; flex-wrap:wrap; gap:1rem; align-items:flex-end; margin-bottom:1.5rem; }
    .filter-group { display:flex; flex-direction:column; gap:0.35rem; }
    .filter-group label { font-size:0.72rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; }
    .filter-group input, .filter-group select { padding:0.55rem 0.9rem; border:1px solid var(--border); border-radius:0.5rem; font-size:0.875rem; background:white; }
    .table-wrap { background:white; border:1px solid var(--border); border-radius:0.75rem; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,0.05); }
    table { width:100%; border-collapse:collapse; }
    thead th { background:#f8fafc; padding:0.75rem 1.25rem; text-align:left; font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; border-bottom:1px solid var(--border); }
    tbody td { padding:1rem 1.25rem; border-bottom:1px solid #f1f5f9; font-size:0.875rem; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover { background:#fafafa; }
    .badge { display:inline-flex; align-items:center; padding:0.2rem 0.65rem; border-radius:999px; font-size:0.72rem; font-weight:700; }
    .badge-pending   { background:#fef3c7; color:#92400e; }
    .badge-contacted { background:#dbeafe; color:#1d4ed8; }
    .badge-converted { background:#dcfce7; color:#15803d; }
    .badge-rejected  { background:#fee2e2; color:#dc2626; }
    .status-form select { padding:0.3rem 0.6rem; border:1px solid var(--border); border-radius:0.4rem; font-size:0.78rem; cursor:pointer; }
    .btn-del { background:#fee2e2; color:#dc2626; border:none; padding:0.3rem 0.7rem; border-radius:0.4rem; font-size:0.75rem; font-weight:600; cursor:pointer; }
</style>
@endsection

@section('content')

@if(session('success'))
    <div style="background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:0.875rem 1.25rem; border-radius:0.5rem; margin-bottom:1.5rem;">
        {{ session('success') }}
    </div>
@endif

<div class="filter-bar">
    <form method="GET" action="{{ route('admin.booking-requests.index') }}" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:flex-end;">
        <div class="filter-group">
            <label>Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, phone...">
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
        <a href="{{ route('admin.booking-requests.index') }}" style="padding:0.55rem 1rem; background:#f1f5f9; color:var(--text-main); border-radius:0.5rem; font-size:0.875rem; text-decoration:none; font-weight:500;">Clear</a>
    </form>
    <div style="margin-left:auto; font-size:0.85rem; color:var(--text-muted); align-self:center;">
        {{ $requests->total() }} total requests
    </div>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email / Phone</th>
                <th>Property</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Adults</th>
                <th>Message</th>
                <th>Status</th>
                <th>Submitted</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $req)
                <tr>
                    <td style="color:var(--text-muted);">{{ $req->id }}</td>
                    <td style="font-weight:600;">{{ $req->name }}</td>
                    <td>
                        <div>{{ $req->email }}</div>
                        @if($req->phone) <div style="font-size:0.78rem; color:var(--text-muted);">{{ $req->phone }}</div> @endif
                    </td>
                    <td>
                        <div style="font-weight:500;">{{ $req->property->name ?? '—' }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">{{ $req->vendor->user->name ?? '—' }}</div>
                    </td>
                    <td>{{ $req->check_in->format('d M Y') }}</td>
                    <td>{{ $req->check_out->format('d M Y') }}</td>
                    <td>{{ $req->adults }}</td>
                    <td style="max-width:180px; color:var(--text-muted); font-size:0.8rem;">{{ Str::limit($req->message, 60) }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.booking-requests.status', $req) }}" class="status-form">
                            @csrf @method('PATCH')
                            <select name="status" onchange="this.form.submit()">
                                @foreach(['pending','contacted','converted','rejected'] as $s)
                                    <option value="{{ $s }}" {{ $req->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td style="font-size:0.78rem; color:var(--text-muted);">{{ $req->created_at->format('d M Y') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.booking-requests.destroy', $req) }}" onsubmit="return confirm('Delete this request?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-del">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align:center; padding:3rem; color:var(--text-muted);">No booking requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:1.5rem;">
    {{ $requests->links() }}
</div>

@endsection
