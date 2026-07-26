@extends('layouts.admin')

@section('header', 'Booking Management')

@section('styles')
<style>
    .filter-bar {
        padding: 1.5rem 2rem;
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        min-width: 160px;
    }
    .filter-group label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .filter-group input,
    .filter-group select {
        padding: 0.55rem 0.9rem;
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-family: 'Outfit', sans-serif;
        background: white;
        color: var(--text-main);
        outline: none;
        transition: border-color 0.2s;
    }
    .filter-group input:focus,
    .filter-group select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(73,166,140,0.12);
    }
    .search-wrap {
        flex: 1;
        min-width: 220px;
    }
    .search-wrap input {
        width: 100%;
        padding-left: 2.5rem;
    }
    .search-icon-wrap {
        position: relative;
    }
    .search-icon-wrap svg {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
    }
    .btn-filter {
        padding: 0.55rem 1.25rem;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        border: none;
        font-family: 'Outfit', sans-serif;
        transition: all 0.2s;
    }
    .btn-apply {
        background: var(--primary);
        color: white;
    }
    .btn-apply:hover { background: var(--primary-hover); }
    .btn-reset {
        background: #f1f5f9;
        color: var(--text-main);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .btn-reset:hover { background: #e2e8f0; }
    .status-tabs {
        display: flex;
        gap: 0.5rem;
        padding: 1.25rem 2rem 0;
        flex-wrap: wrap;
    }
    .status-tab {
        padding: 0.4rem 1rem;
        border-radius: 2rem;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--border);
        color: var(--text-muted);
        transition: all 0.2s;
        background: white;
    }
    .status-tab:hover { border-color: var(--primary); color: var(--primary); }
    .status-tab.active { background: var(--primary); color: white; border-color: var(--primary); }
    .action-btns { display: flex; gap: 0.4rem; flex-wrap: wrap; }
    .btn-view { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 0.35rem 0.75rem; border-radius: 0.6rem; font-size: 0.75rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem; transition: all 0.2s; }
    .btn-view:hover { background: #dbeafe; }
    .btn-edit { background: #fefce8; color: #854d0e; border: 1px solid #fef08a; padding: 0.35rem 0.75rem; border-radius: 0.6rem; font-size: 0.75rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem; transition: all 0.2s; }
    .btn-edit:hover { background: #fef9c3; }
    .btn-refund { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; padding: 0.35rem 0.75rem; border-radius: 0.6rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.3rem; transition: all 0.2s; font-family: 'Outfit', sans-serif; }
    .btn-refund:hover { background: #fee2e2; }
    .results-info { padding: 0.75rem 2rem; font-size: 0.8125rem; color: var(--text-muted); border-bottom: 1px solid var(--border); background: white; }
</style>
@endsection

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2>All Bookings</h2>
        <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Booking
        </a>
    </div>

    {{-- Payment Status Quick Tabs --}}
    <div class="status-tabs">
        @php
            $currentPayment = request('payment_status', 'paid');
            $tabParams = request()->except('payment_status', 'page');
        @endphp
        <a href="{{ route('admin.bookings.index', array_merge($tabParams, ['payment_status' => 'paid'])) }}"
           class="status-tab {{ $currentPayment === 'paid' ? 'active' : '' }}">● Paid</a>
        <a href="{{ route('admin.bookings.index', array_merge($tabParams, ['payment_status' => 'pending'])) }}"
           class="status-tab {{ $currentPayment === 'pending' ? 'active' : '' }}">● Pending</a>
        <a href="{{ route('admin.bookings.index', array_merge($tabParams, ['payment_status' => 'refunded'])) }}"
           class="status-tab {{ $currentPayment === 'refunded' ? 'active' : '' }}">● Refunded</a>
        <a href="{{ route('admin.bookings.index', array_merge($tabParams, ['payment_status' => 'all'])) }}"
           class="status-tab {{ $currentPayment === 'all' ? 'active' : '' }}">All</a>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.bookings.index') }}">
        <input type="hidden" name="payment_status" value="{{ $currentPayment }}">
        <div class="filter-bar">
            {{-- Search --}}
            <div class="filter-group search-wrap">
                <label>Search Customer / Booking ID</label>
                <div class="search-icon-wrap">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, phone, booking ID…">
                </div>
            </div>

            {{-- Date From --}}
            <div class="filter-group">
                <label>Check-in From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}">
            </div>

            {{-- Date To --}}
            <div class="filter-group">
                <label>Check-in To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}">
            </div>

            {{-- Booking Status --}}
            <div class="filter-group">
                <label>Booking Status</label>
                <select name="status">
                    <option value="">All Statuses</option>
                    <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div style="display:flex;gap:0.5rem;align-items:flex-end;padding-top:1.4rem;">
                <button type="submit" class="btn-filter btn-apply">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:4px"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    Filter
                </button>
                <a href="{{ route('admin.bookings.index') }}" class="btn-filter btn-reset">✕ Reset</a>
            </div>
        </div>
    </form>

    <div class="results-info">
        Showing <strong>{{ $bookings->firstItem() ?? 0 }}</strong>–<strong>{{ $bookings->lastItem() ?? 0 }}</strong> of <strong>{{ $bookings->total() }}</strong> bookings
    </div>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Property / Vendor</th>
                    <th>Dates</th>
                    <th>Amount</th>
                    <th>GST</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td>
                        <span style="font-weight: 700; color: var(--primary);">{{ $booking->booking_number }}</span>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $booking->created_at->format('d M, Y') }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $booking->customer->name ?? 'Guest' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $booking->customer->email ?? '' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $booking->customer->phone ?? '' }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $booking->property->name ?? $booking->property->title ?? '—' }}</div>
                        <div style="font-size: 0.75rem; color: var(--primary);">By: {{ $booking->vendor->business_name ?? '—' }}</div>
                    </td>
                    <td>
                        <div style="font-size: 0.875rem;">
                            {{ \Carbon\Carbon::parse($booking->check_in)->format('d M') }}
                            – {{ \Carbon\Carbon::parse($booking->check_out)->format('d M, Y') }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} nights
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 700;">₹{{ number_format($booking->final_amount, 2) }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Vendor: ₹{{ number_format($booking->vendor_amount, 2) }}</div>
                        <div style="font-size: 0.75rem; color: #6366f1;">Commission: ₹{{ number_format($booking->commission_amount, 2) }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: {{ $booking->gst_amount > 0 ? '#059669' : 'var(--text-muted)' }};">
                            ₹{{ number_format($booking->gst_amount ?? 0, 2) }}
                        </div>
                    </td>
                    <td>
                        @if($booking->status == 'confirmed')
                            <span class="badge badge-success">Confirmed</span>
                        @elseif($booking->status == 'cancelled')
                            <span class="badge" style="background:#fef2f2;color:#dc2626;">Cancelled</span>
                        @else
                            <span class="badge badge-pending">{{ ucfirst($booking->status) }}</span>
                        @endif

                        <div style="font-size: 0.75rem; margin-top: 0.25rem;">
                            @if($booking->payment_status == 'paid')
                                <span style="color:#059669;">● Paid</span>
                            @elseif($booking->payment_status == 'refunded')
                                <span style="color:#4b5563;">● Refunded</span>
                            @else
                                <span style="color:#d97706;">● Unpaid</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-view">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </a>
                            <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn-edit">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </a>
                            @if($booking->status != 'cancelled')
                            <form action="{{ route('admin.bookings.refund', $booking) }}" method="POST"
                                  onsubmit="return confirm('Cancel & Refund booking {{ $booking->booking_number }}?\n\nThis will:\n• Deduct ₹{{ number_format($booking->vendor_amount) }} from vendor wallet\n• Reverse GST of ₹{{ number_format($booking->gst_amount ?? 0) }}\n• Reverse accounting journal entries\n\nContinue?')">
                                @csrf
                                <button type="submit" class="btn-refund">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.27"/></svg>
                                    Refund
                                </button>
                            </form>
                            @else
                                <span style="font-size:0.75rem;color:var(--text-muted);">Processed</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:3rem;color:var(--text-muted);">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 1rem;display:block;opacity:0.3"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        No bookings found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding: 1.5rem;">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
