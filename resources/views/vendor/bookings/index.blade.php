@extends('layouts.vendor')

@section('header', 'My Bookings')

@section('styles')
<style>
    /* Calendar Modal */
    .cal-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.55);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }
    .cal-modal-overlay.open { opacity: 1; pointer-events: all; }
    .cal-modal {
        background: white;
        border-radius: 1.25rem;
        box-shadow: 0 25px 60px rgba(0,0,0,0.2);
        width: 420px;
        max-width: 95vw;
        padding: 1.75rem;
        transform: translateY(20px) scale(0.97);
        transition: transform 0.25s ease;
    }
    .cal-modal-overlay.open .cal-modal { transform: translateY(0) scale(1); }
    .cal-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }
    .cal-modal-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .cal-close {
        background: #f1f5f9;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1rem;
        color: #64748b;
        transition: background 0.2s;
    }
    .cal-close:hover { background: #e2e8f0; }
    .cal-nav {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    .cal-nav button {
        background: #f1f5f9;
        border: none;
        border-radius: 0.5rem;
        width: 30px;
        height: 30px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        color: #334155;
        transition: background 0.15s;
    }
    .cal-nav button:hover { background: #e2e8f0; }
    .cal-month-label { font-weight: 700; font-size: 0.95rem; color: #0f172a; }
    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 3px;
    }
    .cal-day-name {
        text-align: center;
        font-size: 0.7rem;
        font-weight: 700;
        color: #94a3b8;
        padding: 0.35rem 0;
        text-transform: uppercase;
    }
    .cal-day {
        text-align: center;
        padding: 0.65rem 0.2rem;
        border-radius: 0.45rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: #334155;
        position: relative;
        cursor: default;
    }
    .cal-day.empty { background: none; }
    .cal-day.booked { background: #fee2e2; color: #dc2626; font-weight: 700; cursor: pointer; }
    .cal-day.today { border: 2px solid var(--primary); font-weight: 700; }
    .cal-legend {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        font-size: 0.75rem;
        color: #64748b;
    }
    .cal-legend span { display: inline-flex; align-items: center; gap: 0.35rem; }
    .cal-legend .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
    .cal-legend .dot.booked { background: #dc2626; }
    .cal-legend .dot.today { border: 2px solid var(--primary); background: white; }
    .cal-property-name { font-size: 0.8rem; color: #64748b; margin-bottom: 0.25rem; font-weight: 500; }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Property Booking History</h2>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('vendor.bookings.index') }}">
        <div style="padding: 1.25rem 1.75rem; background: #f8fafc; border-bottom: 1px solid var(--border); display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            <div style="display: flex; flex-direction: column; gap: 0.35rem; min-width: 200px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Property</label>
                <select name="property_id" id="filterPropertyId" style="padding: 0.55rem 0.9rem; border: 1px solid var(--border); border-radius: 0.75rem; font-size: 0.875rem; font-family: 'Outfit', sans-serif; background: white; color: var(--text-main); outline: none;">
                    <option value="">All My Properties</option>
                    @foreach($properties as $prop)
                        <option value="{{ $prop->id }}" {{ request('property_id') == $prop->id ? 'selected' : '' }}>{{ $prop->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:0.5rem; align-items:flex-end;">
                <button type="submit" style="padding: 0.55rem 1.25rem; border-radius: 0.75rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; border: none; font-family: 'Outfit', sans-serif; background: var(--primary); color: white;">
                    Filter
                </button>
                <button type="button" style="padding: 0.55rem 1.25rem; border-radius: 0.75rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; border: none; font-family: 'Outfit', sans-serif; background: #4f46e5; color: white;" onclick="openSelectedPropertyCalendar()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:4px; display:inline; vertical-align:middle;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Open Booking Calendar
                </button>
                <a href="{{ route('vendor.bookings.index') }}" style="padding: 0.55rem 1.25rem; border-radius: 0.75rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; border: 1px solid var(--border); font-family: 'Outfit', sans-serif; background: #f1f5f9; color: var(--text-main); text-decoration: none; display: inline-flex; align-items: center;">✕ Reset</a>
            </div>
        </div>
    </form>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Property</th>
                    <th>Dates</th>
                    <th>My Earning</th>
                    <th>Status</th>
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
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $booking->property->name ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <div style="font-size: 0.875rem;">
                            {{ \Carbon\Carbon::parse($booking->check_in)->format('d M') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('d M, Y') }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--primary);">₹{{ number_format($booking->vendor_amount, 2) }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Total: ₹{{ number_format($booking->final_amount, 2) }}</div>
                    </td>
                    <td>
                        @if($booking->status == 'confirmed')
                            <span class="badge badge-success">Confirmed</span>
                        @elseif($booking->status == 'cancelled')
                            <span class="badge" style="background: #fef2f2; color: #dc2626;">Cancelled</span>
                        @else
                            <span class="badge badge-pending">{{ ucfirst($booking->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">No bookings yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 1.5rem;">
        {{ $bookings->links() }}
    </div>
</div>

{{-- ── Booking Calendar Modal ─────────────────────────────────────────── --}}
<div class="cal-modal-overlay" id="calModalOverlay" onclick="closeCalendarIfOutside(event)">
    <div class="cal-modal" id="calModal">
        <div class="cal-modal-header">
            <div class="cal-modal-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Booking Calendar
            </div>
            <button class="cal-close" onclick="closeCalendar()">✕</button>
        </div>
        <div class="cal-property-name" id="calPropertyName"></div>

        <div class="cal-nav">
            <button onclick="changeMonth(-1)">‹</button>
            <span class="cal-month-label" id="calMonthLabel"></span>
            <button onclick="changeMonth(1)">›</button>
        </div>

        <div class="cal-grid" id="calGrid"></div>

        <div class="cal-legend">
            <span><span class="dot booked"></span> Booked</span>
            <span><span class="dot today"></span> Today</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let calBookedDates = [];
    let calDateInfo = {};
    let calYear = new Date().getFullYear();
    let calMonth = new Date().getMonth();

    const DAY_NAMES = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    const MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const todayStr = new Date().toISOString().split('T')[0];

    function openSelectedPropertyCalendar() {
        const select = document.getElementById('filterPropertyId');
        const propertyId = select.value;
        if (!propertyId) {
            alert('Please select a property first.');
            return;
        }
        const propertyName = select.options[select.selectedIndex].text;
        openCalendar(propertyId, propertyName);
    }

    function openCalendar(propertyId, propertyName) {
        document.getElementById('calPropertyName').textContent = propertyName;
        calYear = new Date().getFullYear();
        calMonth = new Date().getMonth();
        calBookedDates = [];
        calDateInfo = {};
        renderCalendar();
        document.getElementById('calModalOverlay').classList.add('open');

        fetch(`/api/properties/${propertyId}/booked-dates`)
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    calBookedDates = data.booked_dates || [];
                    calDateInfo = data.date_info || {};
                    renderCalendar();
                }
            });
    }

    function closeCalendar() {
        document.getElementById('calModalOverlay').classList.remove('open');
    }

    function closeCalendarIfOutside(e) {
        if (e.target === document.getElementById('calModalOverlay')) closeCalendar();
    }

    function changeMonth(dir) {
        calMonth += dir;
        if (calMonth > 11) { calMonth = 0; calYear++; }
        if (calMonth < 0)  { calMonth = 11; calYear--; }
        renderCalendar();
    }

    function renderCalendar() {
        document.getElementById('calMonthLabel').textContent = `${MONTH_NAMES[calMonth]} ${calYear}`;
        const grid = document.getElementById('calGrid');
        grid.innerHTML = '';

        DAY_NAMES.forEach(d => {
            const el = document.createElement('div');
            el.className = 'cal-day-name';
            el.textContent = d;
            grid.appendChild(el);
        });

        const firstDay = new Date(calYear, calMonth, 1).getDay();
        const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            const el = document.createElement('div');
            el.className = 'cal-day empty';
            grid.appendChild(el);
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${calYear}-${String(calMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const el = document.createElement('div');
            let cls = 'cal-day';
            
            if (calBookedDates.includes(dateStr)) {
                cls += ' booked';
                const infoList = calDateInfo[dateStr] || [];
                let tooltip = '';
                infoList.forEach((info, idx) => {
                    if (idx > 0) tooltip += '\n-------------------\n';
                    tooltip += `Booked by: ${info.name}\nCheck-in: ${info.check_in}\nCheck-out: ${info.check_out}\nBooking ID: ${info.booking_number}`;
                });
                el.title = tooltip;
            } else {
                el.title = dateStr;
            }
            
            if (dateStr === todayStr) cls += ' today';
            el.className = cls;
            el.textContent = d;
            grid.appendChild(el);
        }
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCalendar(); });
</script>
@endsection
