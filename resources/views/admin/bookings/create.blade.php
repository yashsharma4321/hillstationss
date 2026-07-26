@extends('layouts.admin')

@section('header', 'Add New Booking')

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
    .calc-box {
        background: linear-gradient(135deg, #f0fdfa, #ecfdf5);
        border: 1px solid #d1fae5;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-top: 1rem;
    }
    .calc-box h4 { font-size: 0.875rem; font-weight: 700; color: #065f46; margin-bottom: 1rem; }
    .calc-row { display: flex; justify-content: space-between; padding: 0.35rem 0; font-size: 0.875rem; }
    .calc-row.total { font-weight: 800; font-size: 1rem; border-top: 1px solid #a7f3d0; padding-top: 0.75rem; margin-top: 0.25rem; color: var(--primary); }
    .gst-info-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.75rem; padding: 0.875rem 1rem; font-size: 0.8125rem; color: #92400e; margin-top: 0.5rem; }
    .back-link { display: inline-flex; align-items: center; gap: 0.5rem; color: var(--text-muted); text-decoration: none; font-size: 0.875rem; font-weight: 600; margin-bottom: 1.5rem; transition: color 0.2s; }
    .back-link:hover { color: var(--primary); }
</style>
@endsection

@section('content')
<a href="{{ route('admin.bookings.index') }}" class="back-link">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    Back to Bookings
</a>

<div class="content-card">
    <div class="card-header">
        <h2>Add New Booking</h2>
    </div>

    <form action="{{ route('admin.bookings.store') }}" method="POST" style="padding: 2rem;" id="booking-form">
        @csrf

        @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fee2e2;border-radius:0.75rem;padding:1rem;margin-bottom:1.5rem;color:#dc2626;font-size:0.875rem;">
            <ul style="margin:0;padding-left:1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Customer, Property, Vendor --}}
        <div class="form-section">
            <div class="form-section-title">Booking Parties</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Customer *</label>
                    <select name="customer_id" required>
                        <option value="">— Select Customer —</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Property *</label>
                    <select name="property_id" required id="property-select">
                        <option value="">— Select Property —</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}"
                                    data-vendor="{{ $property->vendor_id }}"
                                    data-amount="{{ $property->amount ?? 0 }}"
                                    data-gst="{{ $property->gst ?? 0 }}"
                                    {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name ?? $property->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Vendor *</label>
                    <select name="vendor_id" required id="vendor-select">
                        <option value="">— Select Vendor —</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}"
                                    data-commission="{{ $vendor->commission_rate ?? 10 }}"
                                    {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->business_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Stay Details --}}
        <div class="form-section">
            <div class="form-section-title">Stay Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Check-in Date *</label>
                    <input type="date" name="check_in" id="check_in" value="{{ old('check_in') }}" required min="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label>Check-out Date *</label>
                    <input type="date" name="check_out" id="check_out" value="{{ old('check_out') }}" required>
                </div>
                <div class="form-group">
                    <label>Number of Guests *</label>
                    <input type="number" name="guest_count" id="guest_count" value="{{ old('guest_count', 1) }}" min="1" required>
                </div>
            </div>
        </div>

        {{-- Financials --}}
        <div class="form-section">
            <div class="form-section-title">Financial Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Base Amount (₹) per Night *</label>
                    <input type="number" name="total_amount" id="total_amount"
                           value="{{ old('total_amount') }}" step="0.01" min="0" required
                           placeholder="e.g. 5000">
                </div>
                <div class="form-group">
                    <label>GST % (from property)</label>
                    <input type="number" name="gst_rate_display" id="gst_rate_display" step="0.01" min="0" max="100"
                           value="{{ old('gst_rate_display', 18) }}" placeholder="18" readonly
                           style="background:#f8fafc;">
                    <div class="gst-info-box">
                        <strong>GST (CGST 9% + SGST 9%)</strong> will be calculated automatically.
                        Auto-populated from property GST setting.
                    </div>
                </div>
                <div class="form-group">
                    <label>GST Amount (₹) — auto-calculated</label>
                    <input type="number" name="gst_amount" id="gst_amount" step="0.01" min="0"
                           value="{{ old('gst_amount', 0) }}" readonly style="background:#f8fafc;color:#059669;font-weight:700;">
                </div>
                <div class="form-group">
                    <label>Commission % *</label>
                    <input type="number" name="commission_percentage" id="commission_percentage"
                           value="{{ old('commission_percentage', 10) }}" step="0.01" min="0" max="100" required>
                </div>
            </div>

            {{-- Live Calculation Box --}}
            <div class="calc-box" id="calc-box">
                <h4>📊 Live Booking Summary</h4>
                <div class="calc-row"><span>Base Amount</span><span id="disp-base">₹0.00</span></div>
                <div class="calc-row"><span>GST (<span id="disp-gst-pct">18</span>%)</span><span id="disp-gst">₹0.00</span></div>
                <div class="calc-row" style="color:#6366f1;"><span>Admin Commission (<span id="disp-comm-pct">10</span>%)</span><span id="disp-comm">₹0.00</span></div>
                <div class="calc-row" style="color:#059669;"><span>Vendor Payout</span><span id="disp-vendor">₹0.00</span></div>
                <div class="calc-row total"><span>Total Payable</span><span id="disp-total">₹0.00</span></div>
            </div>
        </div>

        {{-- Status --}}
        <div class="form-section">
            <div class="form-section-title">Booking Status</div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Booking Status *</label>
                    <select name="status" required>
                        <option value="confirmed" selected>Confirmed</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Status *</label>
                    <select name="payment_status" required>
                        <option value="paid" selected>Paid</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:1rem;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Create Booking
            </button>
            <a href="{{ route('admin.bookings.index') }}" class="btn" style="background:#f1f5f9;color:var(--text-main);">Cancel</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Auto-fill vendor & GST when property is selected
    document.getElementById('property-select').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const vendorId = opt.dataset.vendor;
        const gst = parseFloat(opt.dataset.gst) || 18;

        // Set vendor
        const vSel = document.getElementById('vendor-select');
        for (let i = 0; i < vSel.options.length; i++) {
            if (vSel.options[i].value == vendorId) {
                vSel.selectedIndex = i;
                // auto-set commission rate
                const commRate = parseFloat(vSel.options[i].dataset.commission) || 10;
                document.getElementById('commission_percentage').value = commRate;
                break;
            }
        }

        // Set GST rate
        document.getElementById('gst_rate_display').value = gst;
        recalculate();
    });

    // Auto-set commission when vendor changes
    document.getElementById('vendor-select').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const comm = parseFloat(opt.dataset.commission) || 10;
        document.getElementById('commission_percentage').value = comm;
        recalculate();
    });

    // Recalculate on any financial input change
    ['total_amount', 'gst_rate_display', 'commission_percentage'].forEach(id => {
        document.getElementById(id).addEventListener('input', recalculate);
    });

    function recalculate() {
        const base   = parseFloat(document.getElementById('total_amount').value) || 0;
        const gstPct = parseFloat(document.getElementById('gst_rate_display').value) || 0;
        const commPct = parseFloat(document.getElementById('commission_percentage').value) || 0;

        const gstAmt  = parseFloat((base * gstPct / 100).toFixed(2));
        const total   = parseFloat((base + gstAmt).toFixed(2));
        const comm    = parseFloat((base * commPct / 100).toFixed(2));
        const vendor  = parseFloat((base - comm).toFixed(2));

        // Update hidden GST field
        document.getElementById('gst_amount').value = gstAmt;

        // Update display
        document.getElementById('disp-base').textContent   = '₹' + fmt(base);
        document.getElementById('disp-gst').textContent    = '₹' + fmt(gstAmt);
        document.getElementById('disp-comm').textContent   = '₹' + fmt(comm);
        document.getElementById('disp-vendor').textContent = '₹' + fmt(vendor);
        document.getElementById('disp-total').textContent  = '₹' + fmt(total);
        document.getElementById('disp-gst-pct').textContent  = gstPct;
        document.getElementById('disp-comm-pct').textContent = commPct;
    }

    function fmt(n) {
        return n.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    recalculate();
</script>
@endsection
