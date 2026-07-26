@extends('layouts.admin')

@section('header', 'Edit Booking')

@section('styles')
<style>
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media(max-width:768px){ .form-grid { grid-template-columns: 1fr; } }
    .form-group { display: flex; flex-direction: column; gap: 0.4rem; }
    .form-group label { font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); }
    .form-group input, .form-group select, .form-group textarea {
        padding: 0.65rem 1rem;
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        font-size: 0.9rem;
        font-family: 'Outfit', sans-serif;
        color: var(--text-main);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: white;
    }
    .form-group input:focus, .form-group select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(73,166,140,0.12);
    }
    .form-section { margin-bottom: 2rem; }
    .form-section-title {
        font-size: 0.875rem; font-weight: 700; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.05em;
        margin-bottom: 1rem; padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--border);
    }
    .gst-info { background: #ecfdf5; border: 1px solid #d1fae5; border-radius: 0.75rem; padding: 1rem; font-size: 0.8125rem; color: #065f46; }
    .back-link { display: inline-flex; align-items: center; gap: 0.5rem; color: var(--text-muted); text-decoration: none; font-size: 0.875rem; font-weight: 600; margin-bottom: 1.5rem; transition: color 0.2s; }
    .back-link:hover { color: var(--primary); }
</style>
@endsection

@section('content')
<a href="{{ route('admin.bookings.show', $booking) }}" class="back-link">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    Back to Booking Details
</a>

<div class="content-card">
    <div class="card-header">
        <h2>Edit Booking — {{ $booking->booking_number }}</h2>
    </div>

    <form action="{{ route('admin.bookings.update', $booking) }}" method="POST" style="padding: 2rem;">
        @csrf
        @method('PUT')

        @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fee2e2;border-radius:0.75rem;padding:1rem;margin-bottom:1.5rem;color:#dc2626;font-size:0.875rem;">
            <ul style="margin:0;padding-left:1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Stay Dates --}}
        <div class="form-section">
            <div class="form-section-title">Stay Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Check-in Date *</label>
                    <input type="date" name="check_in" value="{{ old('check_in', \Carbon\Carbon::parse($booking->check_in)->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label>Check-out Date *</label>
                    <input type="date" name="check_out" value="{{ old('check_out', \Carbon\Carbon::parse($booking->check_out)->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label>Guest Count *</label>
                    <input type="number" name="guest_count" value="{{ old('guest_count', $booking->guest_count) }}" min="1" required>
                </div>
            </div>
        </div>

        {{-- Financial --}}
        <div class="form-section">
            <div class="form-section-title">Financial Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Final Amount (₹) *</label>
                    <input type="number" name="final_amount" value="{{ old('final_amount', $booking->final_amount) }}" step="0.01" min="0" required id="final_amount">
                </div>
                <div class="form-group">
                    <label>Vendor Amount (₹) *</label>
                    <input type="number" name="vendor_amount" value="{{ old('vendor_amount', $booking->vendor_amount) }}" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Commission Amount (₹) *</label>
                    <input type="number" name="commission_amount" value="{{ old('commission_amount', $booking->commission_amount) }}" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>GST Amount (₹)</label>
                    <input type="number" name="gst_amount" value="{{ old('gst_amount', $booking->gst_amount ?? 0) }}" step="0.01" min="0" id="gst_amount">
                    <div class="gst-info" style="margin-top:0.5rem;">
                        💡 GST = CGST (9%) + SGST (9%). Enter total GST. It will be split equally.
                    </div>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="form-section">
            <div class="form-section-title">Status</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Booking Status *</label>
                    <select name="status" required>
                        <option value="pending"   {{ old('status', $booking->status) == 'pending'   ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Status *</label>
                    <select name="payment_status" required>
                        <option value="pending"  {{ old('payment_status', $booking->payment_status) == 'pending'  ? 'selected' : '' }}>Pending</option>
                        <option value="paid"     {{ old('payment_status', $booking->payment_status) == 'paid'     ? 'selected' : '' }}>Paid</option>
                        <option value="refunded" {{ old('payment_status', $booking->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:1rem;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Save Changes
            </button>
            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn" style="background:#f1f5f9;color:var(--text-main);">Cancel</a>
        </div>
    </form>
</div>
@endsection
