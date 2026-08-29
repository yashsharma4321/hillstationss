@extends('layouts.vendor')

@section('header', 'Property Calendar: ' . $property->name)

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1, #8b5cf6);
        --radius-sm: 0.5rem;
        --radius-md: 0.75rem;
        --radius-lg: 1rem;
    }

    .calendar-container {
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
    }

    @media (max-width: 992px) {
        .calendar-container {
            flex-direction: column;
        }
    }

    .calendar-sidebar {
        width: 320px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        flex-shrink: 0;
    }

    .calendar-main {
        flex: 1;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        min-width: 0;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1e293b;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #e2e8f0;
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.875rem;
        color: #1e293b;
        background-color: #fafbfc;
    }

    .btn-add-primary {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 0.75rem 1.25rem;
        border-radius: var(--radius-md);
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 600;
        width: 100%;
        margin-top: 1rem;
        transition: all 0.2s ease;
    }

    .btn-add-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    #special_dates_feedback {
        margin-top: 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        text-align: center;
    }

    /* FullCalendar Customizations */
    .fc-event {
        cursor: pointer;
    }
    .fc-bg-event {
        opacity: 0.8 !important;
        font-weight: 600;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .fc-daygrid-event {
        white-space: normal !important;
        align-items: center;
    }
    
    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #64748b;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-sm);
        background: white;
    }
    .btn-back:hover {
        background: #f8fafc;
        color: #1e293b;
    }
</style>
@endsection

@section('content')
<div class="header-actions">
    <a href="{{ route('vendor.properties.edit', $property) }}" class="btn-back">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"></path></svg>
        Back to Property Edit
    </a>
    <div style="display: flex; gap: 1rem; align-items: center; background: white; border: 1px solid #e2e8f0; padding: 0.5rem 1rem; border-radius: var(--radius-sm); box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <span style="font-size: 0.8rem; font-weight: 600; color: #475569; display: flex; align-items: center; gap: 0.35rem;">
            <span style="width: 12px; height: 12px; background: #3b82f6; border-radius: 3px; display: inline-block;"></span> Bookings
        </span>
        <span style="font-size: 0.8rem; font-weight: 600; color: #475569; display: flex; align-items: center; gap: 0.35rem;">
            <span style="width: 12px; height: 12px; background: #e0e7ff; border: 1px solid #c7d2fe; border-radius: 3px; display: inline-block;"></span> Special Rates
        </span>
        <span style="font-size: 0.8rem; font-weight: 600; color: #475569; display: flex; align-items: center; gap: 0.35rem;">
            <span style="width: 12px; height: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 3px; display: inline-block;"></span> Standard Price
        </span>
    </div>
</div>

<div class="calendar-container">
    <div class="calendar-sidebar">
        <h3 style="font-size: 1.1rem; color: #1e293b; margin-bottom: 1.25rem;">Set Pricing / Status</h3>
        
        <div class="form-group">
            <label class="form-label">Select Type</label>
            <select id="special_type" class="form-input">
                <option value="special_price">Special Date Price</option>
                <option value="maintenance">Maintenance Mode</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Select Date Range</label>
            <input type="text" id="special_date_range" class="form-input" placeholder="Select dates..." readonly>
        </div>
        
        <div class="form-group" style="display: none;">
            <label class="form-label">From Date</label>
            <input type="text" id="special_from_date" class="form-input" readonly>
        </div>
        
        <div class="form-group" style="display: none;">
            <label class="form-label">To Date</label>
            <input type="text" id="special_to_date" class="form-input" readonly>
        </div>
        
        <div class="form-group" id="special_amount_group">
            <label class="form-label">Amount (₹) <span style="color:#ef4444">*</span></label>
            <input type="number" id="special_amount" class="form-input" min="0" step="0.01" placeholder="e.g. 15000">
        </div>
        
        <div class="form-group">
            <label class="form-label">Label (Optional)</label>
            <input type="text" id="special_label" class="form-input" placeholder="e.g. Weekend">
        </div>

        <div class="form-group">
            <label class="form-label">Apply to Days</label>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; font-size: 0.85rem;">
                <label><input type="checkbox" class="day-checkbox" value="0" checked> Sunday</label>
                <label><input type="checkbox" class="day-checkbox" value="1" checked> Monday</label>
                <label><input type="checkbox" class="day-checkbox" value="2" checked> Tuesday</label>
                <label><input type="checkbox" class="day-checkbox" value="3" checked> Wednesday</label>
                <label><input type="checkbox" class="day-checkbox" value="4" checked> Thursday</label>
                <label><input type="checkbox" class="day-checkbox" value="5" checked> Friday</label>
                <label><input type="checkbox" class="day-checkbox" value="6" checked> Saturday</label>
            </div>
        </div>

        <button type="button" class="btn-add-primary" id="btn_save_special_dates">Save Dates</button>
        <div id="special_dates_feedback"></div>
    </div>

    <div class="calendar-main">
        <div id="calendar"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Flatpickr setup
        flatpickr("#special_date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    document.getElementById('special_from_date').value = instance.formatDate(selectedDates[0], "Y-m-d");
                    document.getElementById('special_to_date').value = instance.formatDate(selectedDates[1], "Y-m-d");
                } else if (selectedDates.length === 1) {
                    document.getElementById('special_from_date').value = instance.formatDate(selectedDates[0], "Y-m-d");
                    document.getElementById('special_to_date').value = instance.formatDate(selectedDates[0], "Y-m-d");
                } else {
                    document.getElementById('special_from_date').value = '';
                    document.getElementById('special_to_date').value = '';
                }
            }
        });

        // FullCalendar setup
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            selectable: true,
            dateClick: function(info) {
                const fp = document.getElementById('special_date_range')._flatpickr;
                if (fp) {
                    fp.setDate([info.dateStr, info.dateStr], true);
                    const amountInput = document.getElementById('special_amount');
                    if (amountInput) {
                        amountInput.focus();
                    }
                }
            },
            eventClick: function(info) {
                if (info.event.url) {
                    window.location.href = info.event.url;
                    info.jsEvent.preventDefault();
                }
            },
            events: '{{ route('vendor.properties.calendar_events', $property) }}',
            eventContent: function(arg) {
                if (arg.event.display === 'background') {
                    return { html: '<div class="fc-bg-event-title" style="color:' + arg.event.textColor + '">' + arg.event.title + '</div>' };
                }
                return {
                    html: '<div class="fc-event-title-container" style="padding: 2px 4px; font-size: 0.75rem; font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: flex; align-items: center; gap: 4px; cursor: pointer; color: white;">' +
                          '<span>📅</span>' +
                          '<span>' + arg.event.title + '</span>' +
                          '</div>'
                };
            }
        });
        calendar.render();

        // Type Change dynamic toggle
        const typeSelect = document.getElementById('special_type');
        const amountGroup = document.getElementById('special_amount_group');
        if (typeSelect && amountGroup) {
            typeSelect.addEventListener('change', function() {
                if (this.value === 'maintenance') {
                    amountGroup.style.display = 'none';
                } else {
                    amountGroup.style.display = 'block';
                }
            });
        }

        // AJAX Save
        document.getElementById('btn_save_special_dates')?.addEventListener('click', async function() {
            const type = document.getElementById('special_type').value;
            const fromDate = document.getElementById('special_from_date').value;
            const toDate = document.getElementById('special_to_date').value;
            const amount = document.getElementById('special_amount').value;
            const label = document.getElementById('special_label').value;
            const feedback = document.getElementById('special_dates_feedback');

            const selectedDays = Array.from(document.querySelectorAll('.day-checkbox:checked')).map(cb => parseInt(cb.value));

            if (!fromDate || !toDate) {
                feedback.innerHTML = '<span style="color:#dc2626;">Please select a date range.</span>';
                return;
            }
            if (type === 'special_price' && !amount) {
                feedback.innerHTML = '<span style="color:#dc2626;">Please enter an amount.</span>';
                return;
            }
            if (selectedDays.length === 0) {
                feedback.innerHTML = '<span style="color:#dc2626;">Please select at least one day of the week.</span>';
                return;
            }

            feedback.innerHTML = '<span style="color:#2563eb;">Saving...</span>';

            try {
                const response = await fetch(`{{ route('vendor.properties.ajax_special_dates', $property) }}`, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        from_date: fromDate, 
                        to_date: toDate, 
                        amount: type === 'maintenance' ? 0 : amount, 
                        label: label,
                        days: selectedDays,
                        is_open: type === 'maintenance' ? 0 : 1,
                        type: type
                    })
                });

                const data = await response.json();
                
                if (response.ok && data.success) {
                    feedback.innerHTML = '<span style="color:#16a34a;">' + data.message + '</span>';
                    calendar.refetchEvents(); // Refresh calendar events on success
                    setTimeout(() => feedback.innerHTML = '', 3000);
                } else {
                    const errMsg = data.message || 'Error saving dates.';
                    feedback.innerHTML = '<span style="color:#dc2626;">' + errMsg + '</span>';
                }
            } catch (error) { 
                feedback.innerHTML = '<span style="color:#dc2626;">Failed to save. ' + error.message + '</span>';
            }
        });
    });
</script>
@endsection
