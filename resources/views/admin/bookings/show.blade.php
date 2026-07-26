@extends('layouts.admin')

@section('header', 'Booking Details')

@section('styles')
<style>
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; padding: 2rem; }
    @media(max-width:768px){ .detail-grid { grid-template-columns: 1fr; } }
    .detail-section { background: #f8fafc; border-radius: 1rem; padding: 1.5rem; border: 1px solid var(--border); }
    .detail-section h3 { font-size: 0.875rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .detail-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 0.5rem 0; border-bottom: 1px solid var(--border); }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { font-size: 0.8125rem; color: var(--text-muted); font-weight: 500; }
    .detail-value { font-size: 0.875rem; font-weight: 600; text-align: right; max-width: 60%; }
    .amount-highlight { font-size: 1.5rem; font-weight: 800; color: var(--primary); }
    .gst-row { background: #ecfdf5; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-top: 0.75rem; }
    .gst-breakdown { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem; margin-top: 0.5rem; }
    .gst-item { background: white; border-radius: 0.5rem; padding: 0.5rem; text-align: center; border: 1px solid #d1fae5; }
    .gst-item-label { font-size: 0.7rem; color: #059669; font-weight: 600; }
    .gst-item-value { font-size: 0.875rem; font-weight: 700; color: #065f46; }
    .back-link { display: inline-flex; align-items: center; gap: 0.5rem; color: var(--text-muted); text-decoration: none; font-size: 0.875rem; font-weight: 600; margin-bottom: 1.5rem; transition: color 0.2s; }
    .back-link:hover { color: var(--primary); }
    .journal-table td, .journal-table th { padding: 0.75rem 1rem; font-size: 0.8125rem; }
    .status-badge-lg { padding: 0.5rem 1.25rem; border-radius: 2rem; font-size: 0.9rem; font-weight: 700; }
</style>
@endsection

@section('content')
<a href="{{ route('admin.bookings.index') }}" class="back-link">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    Back to Bookings
</a>

<div class="content-card">
    <div class="card-header">
        <div>
            <h2>{{ $booking->booking_number }}</h2>
            <div style="font-size:0.8125rem;color:var(--text-muted);margin-top:0.25rem;">
                Created {{ $booking->created_at->format('d M Y, h:i A') }}
            </div>
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
            @if($booking->status == 'confirmed')
                <span class="badge badge-success status-badge-lg">✓ Confirmed</span>
            @elseif($booking->status == 'cancelled')
                <span class="badge status-badge-lg" style="background:#fef2f2;color:#dc2626;">✕ Cancelled</span>
            @else
                <span class="badge badge-pending status-badge-lg">⏳ {{ ucfirst($booking->status) }}</span>
            @endif

            @if($booking->payment_status == 'paid')
                <span class="badge status-badge-lg" style="background:#ecfdf5;color:#059669;">● Paid</span>
            @elseif($booking->payment_status == 'refunded')
                <span class="badge status-badge-lg" style="background:#f1f5f9;color:#4b5563;">↩ Refunded</span>
            @else
                <span class="badge badge-pending status-badge-lg">● Unpaid</span>
            @endif

            <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-primary" style="padding:0.5rem 1rem;font-size:0.875rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
            </a>

            @if($booking->status != 'cancelled')
            <form action="{{ route('admin.bookings.refund', $booking) }}" method="POST"
                  onsubmit="return confirm('Cancel & Refund {{ $booking->booking_number }}?\n\n• Deduct ₹{{ number_format($booking->vendor_amount) }} from vendor wallet\n• Reverse GST of ₹{{ number_format($booking->gst_amount ?? 0) }}\n• Reverse all journal entries\n\nProceed?')">
                @csrf
                <button type="submit" class="btn" style="background:#fef2f2;color:#dc2626;border:1px solid #fee2e2;padding:0.5rem 1rem;font-size:0.875rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.27"/></svg>
                    Cancel & Refund
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="detail-grid">
        {{-- Customer Info --}}
        <div class="detail-section">
            <h3>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Customer
            </h3>
            <div class="detail-row">
                <span class="detail-label">Name</span>
                <span class="detail-value">{{ $booking->customer->name ?? 'Guest' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email</span>
                <span class="detail-value">{{ $booking->customer->email ?? '—' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone</span>
                <span class="detail-value">{{ $booking->customer->phone ?? '—' }}</span>
            </div>
        </div>

        {{-- Property & Vendor --}}
        <div class="detail-section">
            <h3>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Property & Vendor
            </h3>
            <div class="detail-row">
                <span class="detail-label">Property</span>
                <span class="detail-value">{{ $booking->property->name ?? $booking->property->title ?? '—' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Vendor</span>
                <span class="detail-value">{{ $booking->vendor->business_name ?? '—' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Check-in</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in)->format('D, d M Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Check-out</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($booking->check_out)->format('D, d M Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Duration</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} nights</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Guests</span>
                <span class="detail-value">{{ $booking->guest_count ?? '—' }}</span>
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="detail-section">
            <h3>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Financial Breakdown
            </h3>
            <div class="detail-row">
                <span class="detail-label">Base Amount</span>
                <span class="detail-value">₹{{ number_format($booking->total_amount, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Discount</span>
                <span class="detail-value" style="color:#dc2626;">- ₹{{ number_format($booking->discount_amount ?? 0, 2) }}</span>
            </div>
            @if($booking->coupon)
            <div class="detail-row">
                <span class="detail-label">Coupon Used</span>
                <span class="detail-value" style="color:#7c3aed;">{{ $booking->coupon->code }}</span>
            </div>
            @endif

            {{-- GST Section --}}
            <div class="gst-row">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:0.8125rem;font-weight:700;color:#059669;">GST</span>
                    <span style="font-weight:700;color:#065f46;">+ ₹{{ number_format($booking->gst_amount ?? 0, 2) }}</span>
                </div>
                @php
                    $gst = $booking->gst_amount ?? 0;
                    $cgst = round($gst / 2, 2);
                    $sgst = round($gst / 2, 2);
                @endphp
                @if($gst > 0)
                <div class="gst-breakdown">
                    <div class="gst-item">
                        <div class="gst-item-label">CGST (9%)</div>
                        <div class="gst-item-value">₹{{ number_format($cgst, 2) }}</div>
                    </div>
                    <div class="gst-item">
                        <div class="gst-item-label">SGST (9%)</div>
                        <div class="gst-item-value">₹{{ number_format($sgst, 2) }}</div>
                    </div>
                    <div class="gst-item">
                        <div class="gst-item-label">Total GST</div>
                        <div class="gst-item-value">₹{{ number_format($gst, 2) }}</div>
                    </div>
                </div>
                @endif
            </div>

            <div class="detail-row" style="margin-top:0.75rem;">
                <span class="detail-label" style="font-weight:700;font-size:0.9rem;">Final Amount</span>
                <span class="amount-highlight">₹{{ number_format($booking->final_amount, 2) }}</span>
            </div>
        </div>

        {{-- Commission & Vendor Split --}}
        <div class="detail-section">
            <h3>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                Commission & Payout
            </h3>
            <div class="detail-row">
                <span class="detail-label">Commission Rate</span>
                <span class="detail-value">{{ $booking->commission_percentage ?? 0 }}%</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Admin Commission</span>
                <span class="detail-value" style="color:#6366f1;">₹{{ number_format($booking->commission_amount, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Vendor Payout</span>
                <span class="detail-value" style="color:#059669;font-size:1rem;">₹{{ number_format($booking->vendor_amount, 2) }}</span>
            </div>
            @if($booking->status == 'cancelled')
            <div style="margin-top:1rem;background:#fef2f2;border-radius:0.75rem;padding:1rem;border:1px solid #fee2e2;">
                <div style="font-size:0.8125rem;font-weight:700;color:#dc2626;">↩ Refund Info</div>
                <div style="font-size:0.8125rem;color:#7f1d1d;margin-top:0.5rem;">
                    Vendor wallet debited: ₹{{ number_format($booking->vendor_amount, 2) }}<br>
                    GST reversed: ₹{{ number_format($booking->gst_amount ?? 0, 2) }}<br>
                    All journal entries reversed.
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
