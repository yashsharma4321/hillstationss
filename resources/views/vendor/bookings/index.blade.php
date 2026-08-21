@extends('layouts.vendor')

@section('header', 'My Bookings')

@section('styles')
<style>
    /* List View specific */
    #listView {
        transition: opacity 0.3s ease;
    }

    /* Full Page Calendar Layout */
    #calendarView {
        display: none;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        min-height: 80vh;
        overflow: hidden;
    }

    .calendar-container {
        display: flex;
        height: 100%;
        min-height: 80vh;
    }

    /* Calendar Left Side */
    .calendar-main {
        flex: 1;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #e2e8f0;
    }

    .calendar-header-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .calendar-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    
    .calendar-nav-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .calendar-nav-buttons button {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #475569;
        transition: all 0.2s;
    }

    .calendar-nav-buttons button:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    /* Calendar Grid */
    .cal-grid-full {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        flex: 1;
    }

    .cal-day-header {
        text-align: left;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .cal-day-header:last-child {
        border-right: none;
    }

    .cal-cell {
        min-height: 120px;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
        background: #fff;
        transition: background 0.15s;
    }
    .cal-cell:nth-child(7n) {
        border-right: none;
    }
    .cal-cell:hover {
        background: #f8fafc;
    }
    .cal-cell.empty {
        background: #fcfcfc;
    }

    .cal-date-num {
        font-size: 0.9rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.5rem;
    }
    .cal-cell.today .cal-date-num {
        color: #4f46e5;
        background: #e0e7ff;
        display: inline-block;
        width: 24px;
        height: 24px;
        text-align: center;
        line-height: 24px;
        border-radius: 50%;
    }

    .cal-status-available {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: auto;
        text-align: right;
    }
    .cal-price {
        font-size: 0.85rem;
        font-weight: 600;
        color: #0f172a;
        text-align: right;
        margin-top: 0.2rem;
    }

    .cal-booking-bar {
        background: #3366cc;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        margin-top: 0.2rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        display: block;
        text-decoration: none;
        margin-left: -0.75rem;
        margin-right: -0.75rem;
        border-radius: 0;
    }
    .cal-booking-bar:hover {
        background: #254b99;
    }

    /* Sidebar Right Side */
    .calendar-sidebar {
        width: 320px;
        background: #fff;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .sidebar-header {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 1rem;
    }
    
    .sidebar-close-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #64748b;
        cursor: pointer;
        padding: 0.5rem;
    }
    .sidebar-close-btn:hover {
        color: #0f172a;
    }

    .sidebar-tabs {
        display: flex;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }
    .sidebar-tab {
        flex: 1;
        text-align: center;
        padding: 0.75rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        border-bottom: 2px solid transparent;
    }
    .sidebar-tab.active {
        color: #0ea5e9;
        border-bottom-color: #0ea5e9;
    }
    .sidebar-tab svg {
        display: block;
        margin: 0 auto 0.25rem;
        width: 18px;
        height: 18px;
    }

    .sidebar-section {
        margin-bottom: 2rem;
    }
    .sidebar-section h3 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 1rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 500;
        color: #334155;
        margin-bottom: 0.5rem;
    }

    .toggle-switch {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.85rem;
        color: #334155;
        cursor: pointer;
    }
    .toggle-switch input {
        appearance: none;
        width: 36px;
        height: 20px;
        background: #cbd5e1;
        border-radius: 20px;
        position: relative;
        outline: none;
        cursor: pointer;
        transition: background 0.2s;
    }
    .toggle-switch input::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 16px;
        height: 16px;
        background: white;
        border-radius: 50%;
        transition: transform 0.2s;
    }
    .toggle-switch input:checked {
        background: #0ea5e9;
    }
    .toggle-switch input:checked::after {
        transform: translateX(16px);
    }

    .form-control-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.85rem;
        color: #334155;
        outline: none;
        background: #f8fafc;
    }
    
    .sidebar-helper-text {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.5rem;
        line-height: 1.4;
    }
    .sidebar-helper-text a {
        color: #0ea5e9;
        text-decoration: none;
    }

    .feedback-section {
        margin-top: auto;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: #64748b;
    }
    .feedback-btn {
        background: none;
        border: none;
        color: #64748b;
        cursor: pointer;
        font-size: 1rem;
    }
    .feedback-btn:hover {
        color: #0f172a;
    }
    
    @media print {
        aside, header, #listView, .calendar-sidebar, .calendar-nav-buttons, .navbar, .sidebar { display: none !important; }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        body { background: white !important; }
        .calendar-main { border: none !important; padding: 0 !important; width: 100% !important; }
        .cal-cell { page-break-inside: avoid; }
    }
        .calendar-main { border: none !important; padding: 0 !important; }
        .cal-cell { page-break-inside: avoid; }
    }
</style>
@endsection

@section('content')

{{-- ── List View ─────────────────────────────────────────── --}}
<div class="card" id="listView">
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

{{-- ── Full Page Calendar View ─────────────────────────────────────────── --}}
<div id="calendarView">
    <div class="calendar-container">
        <!-- Left Side: Calendar -->
        <div class="calendar-main">
            <div class="calendar-header-top">
                <h2 class="calendar-title" id="calMonthLabel">August 2026</h2>
                <div class="calendar-nav-buttons">
                    <button onclick="window.print()" title="Download / Print Calendar">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </button>
                    <button onclick="changeMonth(-1)">‹</button>
                    <button onclick="changeMonth(1)">›</button>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="cal-grid-full" id="calGrid">
                <!-- Grid will be generated via JS -->
            </div>
        </div>

        <!-- Right Side: Sidebar -->
        <div class="calendar-sidebar">
            <div class="sidebar-header">
                <button class="sidebar-close-btn" onclick="closeCalendar()">✕</button>
            </div>
            
            <div class="sidebar-section" id="specialPriceSection" style="display: none; border-top:1px solid #e2e8f0; padding-top:1.5rem;">
                <p style="font-size:0.85rem; color:#64748b; margin-bottom:0.5rem; font-weight: 500;">Special Price</p>
                <h3 id="selectedDateLabel" style="font-size:1.1rem; margin-bottom: 1rem;">Select a date</h3>
                
                <div class="form-group">
                    <label>Amount (₹)</label>
                    <input type="number" id="specialPriceInput" class="form-control-select" placeholder="e.g. 35000">
                </div>

                <button type="button" id="saveSpecialPriceBtn" style="width: 100%; padding: 0.75rem; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                    Save Price
                </button>
            </div>
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
    let defaultPrice = 0;
    let specialPrices = {};
    let selectedDateForPrice = null;

    document.addEventListener('DOMContentLoaded', function() {
        const saveBtn = document.getElementById('saveSpecialPriceBtn');
        if(saveBtn) {
            saveBtn.addEventListener('click', function() {
                if(!selectedDateForPrice) return;
                const select = document.getElementById('filterPropertyId');
                const currentPropertyId = select.value;
                let newAmount = parseFloat(document.getElementById('specialPriceInput').value);
                
                if (!isNaN(newAmount) && newAmount >= 0) {
                    const btn = this;
                    btn.textContent = 'Saving...';
                    fetch(`/api/properties/${currentPropertyId}/special-dates`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ date: selectedDateForPrice, amount: newAmount })
                    })
                    .then(res => res.json())
                    .then(response => {
                        btn.textContent = 'Save Price';
                        if(response.status === 'success') {
                            specialPrices[selectedDateForPrice] = newAmount;
                            renderCalendar();
                        } else {
                            alert(response.message || 'Error updating price');
                        }
                    })
                    .catch(err => {
                        btn.textContent = 'Save Price';
                        console.error(err);
                        alert('Error updating price');
                    });
                } else {
                    alert("Please enter a valid amount.");
                }
            });
        }
    });

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
        openCalendar(propertyId);
    }

    function openCalendar(propertyId) {
        calYear = new Date().getFullYear();
        calMonth = new Date().getMonth();
        calBookedDates = [];
        calDateInfo = {};
        
        document.getElementById('listView').style.display = 'none';
        document.getElementById('calendarView').style.display = 'block';

        renderCalendar();

        fetch(`/api/properties/${propertyId}/booked-dates`)
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    calBookedDates = data.booked_dates || [];
                    calDateInfo = data.date_info || {};
                    defaultPrice = parseFloat(data.default_price) || 0;
                    specialPrices = data.special_prices || {};
                    renderCalendar();
                }
            });
    }

    function closeCalendar() {
        document.getElementById('calendarView').style.display = 'none';
        document.getElementById('listView').style.display = 'block';
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

        // Add headers
        DAY_NAMES.forEach(d => {
            const el = document.createElement('div');
            el.className = 'cal-day-header';
            el.textContent = d;
            grid.appendChild(el);
        });

        const firstDay = new Date(calYear, calMonth, 1).getDay();
        const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();

        // Empty cells before start of month
        for (let i = 0; i < firstDay; i++) {
            const el = document.createElement('div');
            el.className = 'cal-cell empty';
            grid.appendChild(el);
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${calYear}-${String(calMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const el = document.createElement('div');
            el.className = 'cal-cell';
            if (dateStr === todayStr) {
                el.classList.add('today');
            }
            
            let contentHtml = `<div class="cal-date-num">${d}</div>`;
            
            if (calBookedDates.includes(dateStr)) {
                const infoList = calDateInfo[dateStr] || [];
                infoList.forEach((info) => {
                    let href = `/vendor/bookings/${info.id || ''}`;
                    contentHtml += `<a href="${href}" class="cal-booking-bar" title="Booking ID: ${info.booking_number}">${info.name}</a>`;
                });
            } else {
                contentHtml += `<div class="cal-status-available">Available</div>`;
                
                let displayPrice = defaultPrice;
                if(specialPrices[dateStr]) {
                    displayPrice = parseFloat(specialPrices[dateStr]);
                }
                contentHtml += `<div class="cal-price">₹${displayPrice.toLocaleString('en-IN')}</div>`;
                
                // Add click event for available cells
                el.style.cursor = 'pointer';
                el.onclick = function() {
                    selectedDateForPrice = dateStr;
                    document.getElementById('specialPriceSection').style.display = 'block';
                    document.getElementById('selectedDateLabel').textContent = dateStr;
                    document.getElementById('specialPriceInput').value = displayPrice;
                    document.getElementById('specialPriceInput').focus();
                };
            }
            
            el.innerHTML = contentHtml;
            grid.appendChild(el);
        }
        
        // Fill remaining grid to complete the last row
        const totalCells = firstDay + daysInMonth;
        const remainder = totalCells % 7;
        if (remainder > 0) {
            for (let i = 0; i < (7 - remainder); i++) {
                const el = document.createElement('div');
                el.className = 'cal-cell empty';
                grid.appendChild(el);
            }
        }
    }
</script>
@endsection
