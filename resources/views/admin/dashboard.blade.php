@extends('layouts.admin')

@section('header', 'Dashboard Overview')

@section('styles')
<style>
/* ── KPI Cards ─────────────────────────────────────────────────────────── */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}
.kpi-card {
    background: white;
    border-radius: 1.25rem;
    padding: 1.5rem;
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    transition: transform 0.25s, box-shadow 0.25s;
    position: relative;
    overflow: hidden;
}
.kpi-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--kpi-color, var(--primary));
}
.kpi-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    background: var(--kpi-bg, #f0fdfa);
    color: var(--kpi-color, var(--primary));
    flex-shrink: 0;
}
.kpi-label { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; }
.kpi-value { font-size: 1.6rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em; line-height: 1; }
.kpi-sub { font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.3rem; }
.kpi-sub.up   { color: #059669; }
.kpi-sub.down { color: #dc2626; }

/* ── Chart Cards ───────────────────────────────────────────────────────── */
.chart-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}
.chart-grid-3 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}
@media(max-width: 1100px) {
    .chart-grid, .chart-grid-3 { grid-template-columns: 1fr; }
}
.chart-card {
    background: white;
    border-radius: 1.25rem;
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    overflow: hidden;
}
.chart-card-header {
    padding: 1.25rem 1.5rem 0.75rem;
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid var(--border);
}
.chart-card-header h3 { font-size: 0.9375rem; font-weight: 700; }
.chart-card-header span { font-size: 0.75rem; color: var(--text-muted); font-weight: 500; }
.chart-body { padding: 1.25rem; position: relative; }
canvas { max-width: 100%; }

/* ── Top Vendors Table ─────────────────────────────────────────────────── */
.vendor-row { display: flex; align-items: center; gap: 1rem; padding: 0.7rem 0; border-bottom: 1px solid var(--border); }
.vendor-row:last-child { border-bottom: none; }
.vendor-rank { width: 28px; height: 28px; border-radius: 8px; background: var(--primary-light); color: var(--primary); font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.vendor-name { font-weight: 600; font-size: 0.875rem; flex: 1; }
.vendor-stats { text-align: right; }
.vendor-stats .rev { font-weight: 800; font-size: 0.9rem; color: var(--primary); }
.vendor-stats .bk  { font-size: 0.7rem; color: var(--text-muted); }
.vendor-bar-wrap { height: 4px; background: #f1f5f9; border-radius: 2px; margin-top: 3px; width: 100%; }
.vendor-bar { height: 4px; background: var(--primary); border-radius: 2px; }

/* ── Recent Bookings ───────────────────────────────────────────────────── */
.recent-row { display: flex; align-items: center; gap: 1rem; padding: 0.65rem 0; border-bottom: 1px solid var(--border); }
.recent-row:last-child { border-bottom: none; }
.recent-id { font-weight: 700; font-size: 0.8rem; color: var(--primary); flex-shrink: 0; }
.recent-info { flex: 1; }
.recent-name { font-size: 0.875rem; font-weight: 600; }
.recent-prop { font-size: 0.75rem; color: var(--text-muted); }
.recent-amt { font-weight: 800; font-size: 0.9rem; white-space: nowrap; }

/* ── Mini Stat Chips ──────────────────────────────────────────────────── */
.chip-row { display: flex; gap: 0.6rem; flex-wrap: wrap; padding: 0 1.5rem 1.25rem; }
.chip { padding: 0.3rem 0.85rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; }
</style>
@endsection

@section('content')

{{-- ── Row 1: KPI Cards ──────────────────────────────────────────────── --}}
<div class="kpi-grid">

    {{-- Total Revenue --}}
    <div class="kpi-card" style="--kpi-color:#49a68c;--kpi-bg:#f0fdfa;">
        <div class="kpi-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="kpi-label">Total Revenue (Paid)</div>
        <div class="kpi-value">₹{{ number_format($stats['total_revenue'] / 100000, 2) }}L</div>
        <div class="kpi-sub {{ $stats['revenue_growth'] >= 0 ? 'up' : 'down' }}">
            @if($stats['revenue_growth'] >= 0)
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
            @else
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            @endif
            {{ abs($stats['revenue_growth']) }}% vs last month
        </div>
    </div>

    {{-- Admin Commission --}}
    <div class="kpi-card" style="--kpi-color:#6366f1;--kpi-bg:#eef2ff;">
        <div class="kpi-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
        <div class="kpi-label">Admin Commission</div>
        <div class="kpi-value" style="color:#6366f1;">₹{{ number_format($stats['total_commission'] / 1000, 1) }}K</div>
        <div class="kpi-sub">Net earnings from bookings</div>
    </div>

    {{-- Vendor Payout --}}
    <div class="kpi-card" style="--kpi-color:#f59e0b;--kpi-bg:#fffbeb;">
        <div class="kpi-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="kpi-label">Total Vendor Payout</div>
        <div class="kpi-value" style="color:#f59e0b;">₹{{ number_format($stats['total_vendor_payout'] / 1000, 1) }}K</div>
        <div class="kpi-sub">Paid to all vendors</div>
    </div>

    {{-- GST Collected --}}
    <div class="kpi-card" style="--kpi-color:#10b981;--kpi-bg:#ecfdf5;">
        <div class="kpi-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 14l6-6M9.5 9a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zM14.5 14a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/><path d="M21 21H3V3"/></svg>
        </div>
        <div class="kpi-label">GST Collected</div>
        <div class="kpi-value" style="color:#10b981;">₹{{ number_format($stats['total_gst'] / 1000, 1) }}K</div>
        <div class="kpi-sub">CGST + SGST from paid bookings</div>
    </div>

    {{-- Total Bookings --}}
    <div class="kpi-card" style="--kpi-color:#0ea5e9;--kpi-bg:#f0f9ff;">
        <div class="kpi-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="kpi-label">Total Bookings</div>
        <div class="kpi-value" style="color:#0ea5e9;">{{ number_format($stats['total_bookings']) }}</div>
        <div class="kpi-sub">
            <span style="color:#059669;">{{ $stats['paid_bookings'] }} paid</span> &nbsp;·&nbsp;
            <span style="color:#d97706;">{{ $stats['pending_bookings'] }} pending</span>
        </div>
    </div>

    {{-- Enquiries --}}
    <div class="kpi-card" style="--kpi-color:#ec4899;--kpi-bg:#fdf2f8;">
        <div class="kpi-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
        <div class="kpi-label">Total Enquiries</div>
        <div class="kpi-value" style="color:#ec4899;">{{ number_format($stats['total_enquiries']) }}</div>
        <div class="kpi-sub">Customer contact requests</div>
    </div>

    {{-- Booking Requests --}}
    <div class="kpi-card" style="--kpi-color:#14b8a6;--kpi-bg:#f0fdfa;">
        <div class="kpi-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
        <div class="kpi-label">Booking Requests</div>
        <div class="kpi-value" style="color:#14b8a6;">{{ number_format($stats['total_booking_requests']) }}</div>
        <div class="kpi-sub">{{ $stats['pending_booking_requests'] }} pending requests</div>
    </div>

    {{-- Refunded --}}
    <div class="kpi-card" style="--kpi-color:#ef4444;--kpi-bg:#fef2f2;">
        <div class="kpi-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.27"/></svg>
        </div>
        <div class="kpi-label">Total Refunded</div>
        <div class="kpi-value" style="color:#ef4444;">₹{{ number_format($stats['total_refunded'] / 1000, 1) }}K</div>
        <div class="kpi-sub">{{ $stats['refunded_bookings'] }} refunded bookings</div>
    </div>

    {{-- Customers --}}
    <div class="kpi-card" style="--kpi-color:#8b5cf6;--kpi-bg:#f5f3ff;">
        <div class="kpi-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div class="kpi-label">Total Customers</div>
        <div class="kpi-value" style="color:#8b5cf6;">{{ number_format($stats['total_customers']) }}</div>
        <div class="kpi-sub">Registered users</div>
    </div>

</div>

{{-- ── Row 2: Revenue Line Chart + Donut ────────────────────────────────── --}}
<div class="chart-grid">

    {{-- Line Chart: Daily Revenue & Bookings (30 days) --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <h3>📈 Revenue & Bookings — Last 30 Days</h3>
            <span>Daily trend</span>
        </div>
        <div class="chart-body" style="height:280px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Donut: Payment Status --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <h3>🍩 Booking Status</h3>
            <span>Distribution</span>
        </div>
        <div class="chart-body" style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:280px;gap:1.5rem;">
            <canvas id="statusDonut" style="max-height:200px;max-width:200px;"></canvas>
            <div style="display:flex;flex-wrap:wrap;gap:0.75rem;justify-content:center;">
                <span class="chip" style="background:#ecfdf5;color:#059669;">✓ Paid: {{ $statusDist['paid'] ?? 0 }}</span>
                <span class="chip" style="background:#fffbeb;color:#d97706;">⏳ Pending: {{ $statusDist['pending'] ?? 0 }}</span>
                <span class="chip" style="background:#fef2f2;color:#dc2626;">↩ Refunded: {{ $statusDist['refunded'] ?? 0 }}</span>
            </div>
        </div>
    </div>

</div>

{{-- ── Row 3: Monthly Bars + Enquiries + Vendor Split ─────────────────── --}}
<div class="chart-grid-3">

    {{-- Bar Chart: Monthly Bookings --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <h3>📅 Monthly Bookings</h3>
            <span>Last 12 months</span>
        </div>
        <div class="chart-body" style="height:220px;">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    {{-- Bar Chart: Monthly Revenue --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <h3>💰 Monthly Revenue</h3>
            <span>Last 12 months</span>
        </div>
        <div class="chart-body" style="height:220px;">
            <canvas id="monthlyRevenueChart"></canvas>
        </div>
    </div>

    {{-- Bar Chart: Enquiries --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <h3>📧 Enquiries</h3>
            <span>Last 6 months</span>
        </div>
        <div class="chart-body" style="height:220px;">
            <canvas id="enquiryChart"></canvas>
        </div>
    </div>

</div>

{{-- ── Row 4: Commission vs Vendor Split (Area) + Recent Bookings ───────── --}}
<div class="chart-grid" style="margin-bottom:1.5rem;">

    {{-- Stacked Area: Commission vs Vendor --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <h3>🏦 Admin Commission vs Vendor Payout</h3>
            <span>Last 30 days</span>
        </div>
        <div class="chart-body" style="height:260px;">
            <canvas id="splitChart"></canvas>
        </div>
    </div>

    {{-- Recent Bookings --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <h3>🕐 Recent Bookings</h3>
            <a href="{{ route('admin.bookings.index') }}" style="font-size:0.8rem;color:var(--primary);text-decoration:none;font-weight:600;">View All →</a>
        </div>
        <div class="chart-body" style="padding: 0.5rem 1.5rem 1.25rem;">
            @forelse($recentBookings as $rb)
            <div class="recent-row">
                <div class="recent-id">{{ $rb->booking_number }}</div>
                <div class="recent-info">
                    <div class="recent-name">{{ $rb->customer->name ?? 'Guest' }}</div>
                    <div class="recent-prop">{{ $rb->property->name ?? '—' }}</div>
                </div>
                <div style="text-align:right;">
                    <div class="recent-amt">₹{{ number_format($rb->final_amount) }}</div>
                    <div style="font-size:0.7rem;margin-top:2px;">
                        @if($rb->payment_status=='paid')
                            <span style="color:#059669;font-weight:700;">● Paid</span>
                        @elseif($rb->payment_status=='refunded')
                            <span style="color:#dc2626;font-weight:700;">↩ Refunded</span>
                        @else
                            <span style="color:#d97706;font-weight:700;">⏳ Pending</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
                <p style="color:var(--text-muted);font-size:0.875rem;padding:1rem 0;">No bookings yet.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- ── Row 5: Top Vendors ──────────────────────────────────────────────── --}}
<div class="chart-grid-3" style="margin-bottom:1.5rem;">

    <div class="chart-card" style="grid-column: span 2;">
        <div class="chart-card-header">
            <h3>🏆 Top Vendors by Revenue</h3>
            <span>Paid bookings</span>
        </div>
        <div class="chart-body" style="padding:0.5rem 1.5rem 1.25rem;">
            @php $maxVendorRev = $topVendors->max('total_revenue') ?: 1; @endphp
            @forelse($topVendors as $i => $tv)
            <div class="vendor-row">
                <div class="vendor-rank">{{ $i + 1 }}</div>
                <div style="flex:1;">
                    <div class="vendor-name">{{ $tv->vendor->business_name ?? '—' }}</div>
                    <div class="vendor-bar-wrap">
                        <div class="vendor-bar" style="width:{{ round(($tv->total_revenue / $maxVendorRev) * 100) }}%;"></div>
                    </div>
                    <div style="font-size:0.7rem;color:var(--text-muted);margin-top:2px;">
                        Admin commission: ₹{{ number_format($tv->admin_commission) }}
                    </div>
                </div>
                <div class="vendor-stats">
                    <div class="rev">₹{{ number_format($tv->total_revenue) }}</div>
                    <div class="bk">{{ $tv->bookings }} bookings</div>
                    <div style="font-size:0.7rem;color:#059669;font-weight:700;">Payout: ₹{{ number_format($tv->vendor_payout) }}</div>
                </div>
            </div>
            @empty
                <p style="color:var(--text-muted);font-size:0.875rem;padding:1rem 0;">No vendor data yet.</p>
            @endforelse
        </div>
    </div>

    {{-- System Health Quick Links --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <h3>⚡ Quick Actions</h3>
        </div>
        <div class="chart-body" style="display:flex;flex-direction:column;gap:0.75rem;padding:1rem 1.5rem 1.5rem;">
            <a href="{{ route('admin.bookings.create') }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;background:#f0fdfa;border-radius:0.75rem;border:1px solid #d1fae5;text-decoration:none;color:#065f46;font-weight:600;font-size:0.875rem;transition:all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Booking
            </a>
            <a href="{{ route('admin.bookings.index') }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;background:#eff6ff;border-radius:0.75rem;border:1px solid #bfdbfe;text-decoration:none;color:#1e40af;font-weight:600;font-size:0.875rem;transition:all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                All Bookings
            </a>
            <a href="{{ route('admin.vendors.index') }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;background:#fefce8;border-radius:0.75rem;border:1px solid #fef08a;text-decoration:none;color:#854d0e;font-weight:600;font-size:0.875rem;transition:all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                Manage Vendors
            </a>
            <a href="{{ route('admin.accounting.index') }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;background:#f5f3ff;border-radius:0.75rem;border:1px solid #ddd6fe;text-decoration:none;color:#6d28d9;font-weight:600;font-size:0.875rem;transition:all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 9.5h20"/></svg>
                Accounting
            </a>
            <a href="{{ route('admin.withdrawals.index') }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;background:#fef2f2;border-radius:0.75rem;border:1px solid #fee2e2;text-decoration:none;color:#dc2626;font-weight:600;font-size:0.875rem;transition:all 0.2s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Withdrawals
            </a>
        </div>
    </div>

</div>

{{-- Booking Requests --}}
<div class="chart-grid" style="margin-bottom:1.5rem;">
    <div class="chart-card">
        <div class="chart-card-header">
            <h3>Recent Booking Requests</h3>
            <a href="{{ route('admin.booking-requests.index') }}" style="font-size:0.8rem;color:var(--primary);text-decoration:none;font-weight:600;">View All</a>
        </div>
        <div class="chart-body" style="padding:0.5rem 1.5rem 1.25rem;">
            @forelse($recentBookingRequests as $request)
                <div class="recent-row">
                    <div class="recent-id">#{{ $request->id }}</div>
                    <div class="recent-info">
                        <div class="recent-name">{{ $request->name }}</div>
                        <div class="recent-prop">
                            {{ $request->property->name ?? 'N/A' }}
                            @if($request->roomType)
                                · {{ $request->roomType->name }}
                            @endif
                        </div>
                        <div class="recent-prop">
                            {{ $request->check_in->format('d M Y') }} to {{ $request->check_out->format('d M Y') }}
                            · {{ $request->adults }} adult{{ $request->adults > 1 ? 's' : '' }}
                            @if($request->children > 0)
                                , {{ $request->children }} child{{ $request->children > 1 ? 'ren' : '' }}
                            @endif
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div class="recent-amt">₹{{ number_format($request->total_amount) }}</div>
                        <div style="font-size:0.7rem;margin-top:2px;text-transform:capitalize;color:#0f766e;font-weight:700;">{{ $request->status }}</div>
                    </div>
                </div>
            @empty
                <p style="color:var(--text-muted);font-size:0.875rem;padding:1rem 0;">No booking requests yet.</p>
            @endforelse
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-card-header">
            <h3>Request Summary</h3>
            <span>Leads from property pages</span>
        </div>
        <div class="chart-body" style="display:flex;align-items:center;justify-content:center;min-height:220px;">
            <a href="{{ route('admin.booking-requests.index') }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1.1rem;background:#f0fdfa;border-radius:0.75rem;border:1px solid #ccfbf1;text-decoration:none;color:#0f766e;font-weight:700;font-size:0.875rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Manage Booking Requests
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = "'Outfit', sans-serif";
    Chart.defaults.color       = '#64748b';

    const labels30   = @json($chartLabels);
    const bookings30 = @json($chartBookings);
    const revenue30  = @json($chartRevenue);
    const comm30     = @json($chartCommission);
    const vendor30   = @json($chartVendor);

    const mLabels   = @json($monthlyLabels);
    const mBookings = @json($monthlyBookings);
    const mRevenue  = @json($monthlyRevenue);

    const eLabels = @json($enquiryLabels);
    const eData   = @json($enquiryData);

    const statusPaid      = {{ $statusDist['paid']     ?? 0 }};
    const statusPending   = {{ $statusDist['pending']  ?? 0 }};
    const statusRefunded  = {{ $statusDist['refunded'] ?? 0 }};

    // ── 1. Revenue & Bookings Line Chart ───────────────────────────────
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: labels30,
            datasets: [
                {
                    label: 'Revenue (₹)',
                    data: revenue30,
                    borderColor: '#49a68c',
                    backgroundColor: 'rgba(73,166,140,0.1)',
                    fill: true,
                    tension: 0.45,
                    pointRadius: 2,
                    yAxisID: 'y',
                },
                {
                    label: 'Bookings',
                    data: bookings30,
                    borderColor: '#6366f1',
                    backgroundColor: 'transparent',
                    fill: false,
                    tension: 0.45,
                    pointRadius: 2,
                    borderDash: [4, 3],
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 10 } } },
                y: {
                    position: 'left', grid: { color: '#f1f5f9' },
                    ticks: { callback: v => '₹' + (v >= 1000 ? (v/1000).toFixed(0)+'K' : v), font: { size: 10 } }
                },
                y1: {
                    position: 'right', grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });

    // ── 2. Status Donut ────────────────────────────────────────────────
    new Chart(document.getElementById('statusDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Pending', 'Refunded'],
            datasets: [{
                data: [statusPaid, statusPending, statusRefunded],
                backgroundColor: ['#49a68c', '#f59e0b', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }, tooltip: { bodyFont: { size: 12 } } },
            cutout: '70%'
        }
    });

    // ── 3. Monthly Bookings Bar ────────────────────────────────────────
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: mLabels,
            datasets: [{
                label: 'Bookings',
                data: mBookings,
                backgroundColor: 'rgba(73,166,140,0.75)',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 9 }, maxRotation: 45 } },
                y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }
            }
        }
    });

    // ── 4. Monthly Revenue Bar ─────────────────────────────────────────
    new Chart(document.getElementById('monthlyRevenueChart'), {
        type: 'bar',
        data: {
            labels: mLabels,
            datasets: [{
                label: 'Revenue (₹)',
                data: mRevenue,
                backgroundColor: 'rgba(99,102,241,0.75)',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 9 }, maxRotation: 45 } },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: { callback: v => '₹' + (v >= 1000 ? (v/1000).toFixed(0)+'K' : v), font: { size: 10 } }
                }
            }
        }
    });

    // ── 5. Enquiries Bar ──────────────────────────────────────────────
    new Chart(document.getElementById('enquiryChart'), {
        type: 'bar',
        data: {
            labels: eLabels,
            datasets: [{
                label: 'Enquiries',
                data: eData,
                backgroundColor: 'rgba(236,72,153,0.7)',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, precision: 0 } }
            }
        }
    });

    // ── 6. Commission vs Vendor Stacked Area ──────────────────────────
    new Chart(document.getElementById('splitChart'), {
        type: 'line',
        data: {
            labels: labels30,
            datasets: [
                {
                    label: 'Admin Commission',
                    data: comm30,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.2)',
                    fill: true, tension: 0.4, pointRadius: 0,
                },
                {
                    label: 'Vendor Payout',
                    data: vendor30,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.15)',
                    fill: true, tension: 0.4, pointRadius: 0,
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 8, font: { size: 10 } } },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: { callback: v => '₹' + (v >= 1000 ? (v/1000).toFixed(0)+'K' : v), font: { size: 10 } }
                }
            }
        }
    });
</script>
@endsection
