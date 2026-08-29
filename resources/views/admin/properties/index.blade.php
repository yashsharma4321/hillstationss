@extends('layouts.admin')

@section('header', 'Properties Management')

@section('styles')
<style>
    .property-card {
        transition: all 0.3s ease;
    }
    .property-card:hover {
        background-color: #f8fafc;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    .status-active { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .status-pending { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .status-other { background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    
    .action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        transition: all 0.2s;
        border: 1px solid var(--border);
        background: white;
        cursor: pointer;
    }
    .action-btn:hover { background: #f1f5f9; border-color: var(--primary); color: var(--primary); }
    .action-btn.delete:hover { border-color: var(--danger); color: var(--danger); }

    .property-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: var(--text-muted);
        font-size: 0.75rem;
        margin-top: 0.5rem;
    }
    .property-meta span {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
</style>
@endsection

@section('content')
<div class="content-card">
    <div class="card-header" style="background: white;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">Property Listings</h2>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">Manage and monitor all your rental properties in one place.</p>
        </div>
        
          <div style="display: flex; gap: 1rem; align-items: stretch; justify-content: flex-end;">
              <button type="button" id="btnBulkSpecialDate" style="background: #10b981; color: white; padding: 0.75rem 1.5rem; border-radius: 0.625rem; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; display: inline-flex; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); transition: all 0.2s; align-items: center; justify-content: center;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 8px -1px rgba(16, 185, 129, 0.3)'" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px -1px rgba(16, 185, 129, 0.2)'" onclick="openBulkModal()">
                 + Add Special Date
              </button>
              <a href="{{ route('admin.properties.create') }}"  
           style="background: var(--primary); color: white; padding: 0.75rem 1.5rem; border-radius: 0.625rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2); transition: all 0.2s;"
           onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 8px -1px rgba(99, 102, 241, 0.3)';"
           onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px -1px rgba(99, 102, 241, 0.2)';">
           + New Property
          </a>
          </div>
      </div>

    @if(session('success'))
        <div style="margin: 1.5rem; padding: 1rem; background: #ecfdf5; color: #065f46; border-radius: 0.5rem; border: 1px solid #a7f3d0; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span style="font-size: 0.875rem; font-weight: 500;">{{ session('success') }}</span>
        </div>
    @endif

    <div id="listView" style="padding: 0 1.5rem 1.5rem 1.5rem;">
        <div style="overflow-x: auto; border: 1px solid var(--border); border-radius: 0.75rem;">
            <table style="min-width: 1000px;">
                <thead>
                    <tr style="background: #f8fafc;">
                          <th style="border-bottom: 1px solid var(--border); width: 40%;">Property Details</th>
                        <th style="border-bottom: 1px solid var(--border);">Location</th>
                        <th style="border-bottom: 1px solid var(--border);">Vendor/Category</th>
                        <th style="border-bottom: 1px solid var(--border);">Status</th>
                        <th style="border-bottom: 1px solid var(--border);">Menu</th>
                        <th style="border-bottom: 1px solid var(--border); text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($properties as $property)
                        <tr class="property-card">
                              <td style="padding: 1.25rem 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    @if(!empty($property->gallery) && isset($property->gallery[0]))
                                        <img src="{{ Storage::url(is_array($property->gallery[0]) ? ($property->gallery[0]['image'] ?? '') : $property->gallery[0]) }}" 
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.5rem; background: #f1f5f9;">
                                    @else
                                        <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: #cbd5e1;">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">{{ $property->name }}</div>
                                        <div class="property-meta">
                                            <span title="Bedrooms"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4v16"/><path d="M2 8h18"/><path d="M22 4v16"/><path d="M2 12h18"/><path d="m18 4-4 4H6L2 4"/></svg> {{ $property->total_bedrooms ?? 0 }} BHK</span>
                                            <span title="Guests"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> {{ $property->max_guests ?? 0 }} Guests</span>
                                            @if($property->is_featured)
                                                <span style="color: #f59e0b; font-weight: 600;">⭐ Featured</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1.25rem 1.5rem;">
                                <div style="font-weight: 600; color: #475569;">{{ $property->destination->name ?? 'N/A' }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $property->city }}, {{ $property->state }}</div>
                            </td>
                            <td style="padding: 1.25rem 1.5rem;">
                                <div style="font-size: 0.875rem; color: #1e293b;">{{ $property->vendor->business_name ?? 'Individual' }}</div>
                                <div style="margin-top: 0.25rem;">
                                    <span style="font-size: 0.7rem; background: #f1f5f9; color: #64748b; padding: 0.1rem 0.5rem; border-radius: 0.25rem; border: 1px solid #e2e8f0;">{{ $property->category->name ?? 'Villa' }}</span>
                                </div>
                            </td>
                            <td style="padding: 1.25rem 1.5rem;">
                                @if($property->status === 'active')
                                    <span class="status-badge status-active">● Live</span>
                                @elseif($property->status === 'pending')
                                    <span class="status-badge status-pending">● Pending</span>
                                @else
                                    <span class="status-badge status-other">● Hidden</span>
                                @endif
                            </td>
                            <td style="padding: 1.25rem 1.5rem;">
                                @if($property->show_in_menu)
                                    <span style="padding: 0.25rem 0.75rem; background: #e0f2fe; color: #0369a1; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #bae6fd;">Visible</span>
                                @else
                                    <span style="padding: 0.25rem 0.75rem; background: #f8fafc; color: #94a3b8; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; border: 1px solid #e2e8f0;">Hidden</span>
                                @endif
                            </td>
                            <td style="padding: 1.25rem 1.5rem; text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: flex-end;">
                                    {{-- Rooms Button --}}
                                    <a href="{{ route('admin.properties.rooms.index', $property) }}"
                                       class="action-btn" title="Manage Rooms"
                                       style="width:auto; padding:0 0.6rem; gap:0.3rem; font-size:0.72rem; font-weight:700; color:#4338ca; border-color:#c7d2fe; background:#eef2ff; text-decoration:none;">
                                        🛏 {{ $property->rooms()->count() }}
                                    </a>
                                    <a href="{{ route('admin.properties.edit', $property) }}" class="action-btn" title="Edit Property">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.properties.destroy', $property) }}" method="POST" onsubmit="return confirm('Delete this property?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete Property">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </button>
                                    </form>
                                    {{-- Calendar Button --}}
                                    <a href="{{ route('admin.properties.calendar', $property) }}"
                                       class="action-btn" title="Open Calendar"
                                       style="color:#0284c7; border-color:#bae6fd; background:#f0f9ff; text-decoration:none;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 5rem 1.5rem;">
                                <div style="margin-bottom: 1rem; color: #cbd5e1;">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto;"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                </div>
                                <h3 style="font-weight: 600; color: #475569;">No properties found</h3>
                                <p style="color: #94a3b8; font-size: 0.875rem; margin-top: 0.5rem;">Start by adding your first villa listing to shown here.</p>
                                
          <div style="display: flex; gap: 1rem;">
              <button type="button" id="btnBulkSpecialDate" style="background: #10b981; color: white; padding: 0.75rem 1.5rem; border-radius: 0.625rem; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; display: none;" onclick="openBulkModal()">
                 + Add Special Date
              </button>
              <a href="{{ route('admin.properties.create') }}"  style="display: inline-block; margin-top: 1.5rem; color: var(--primary); font-weight: 600; text-decoration: none;">+ Add your first property</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($properties->hasPages())
        <div style="margin-top: 1.5rem;">
            {{ $properties->links() }}
        </div>
        @endif
    </div>
</div>





<!-- Full Page Calendar View -->
<div id="calendarView" style="display: none; background: #fff; border-radius: 12px; overflow: hidden;">
    <div style="display: flex; height: 100%; min-height: 80vh;">
        <!-- Left Side: Calendar Placeholder (Visual) -->
        <div style="flex: 1; padding: 2rem; border-right: 1px solid #e2e8f0;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #0f172a;" id="calMonthLabel">Calendar (Visual Reference)</h2>
                <div style="display: flex; gap: 0.5rem;">
                    <button style="padding: 0.5rem 1rem; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;">&lt;</button>
                    <button style="padding: 0.5rem 1rem; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;">&gt;</button>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; gap: 1px; background: #e2e8f0; border: 1px solid #e2e8f0;">
                <div style="background: #f8fafc; padding: 1rem; font-weight: 600;">Sun</div>
                <div style="background: #f8fafc; padding: 1rem; font-weight: 600;">Mon</div>
                <div style="background: #f8fafc; padding: 1rem; font-weight: 600;">Tue</div>
                <div style="background: #f8fafc; padding: 1rem; font-weight: 600;">Wed</div>
                <div style="background: #f8fafc; padding: 1rem; font-weight: 600;">Thu</div>
                <div style="background: #f8fafc; padding: 1rem; font-weight: 600;">Fri</div>
                <div style="background: #f8fafc; padding: 1rem; font-weight: 600;">Sat</div>
                
                
                <script>
                    function renderRealCalendarBulk() {
                        const now = new Date();
                        const year = now.getFullYear();
                        const month = now.getMonth();
                        const firstDay = new Date(year, month, 1).getDay();
                        const daysInMonth = new Date(year, month + 1, 0).getDate();
                        
                        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                        document.getElementById('calMonthLabel').innerText = monthNames[month] + ' ' + year;

                        let html = '';
                        // Fill empty slots
                        for (let i = 0; i < firstDay; i++) {
                            html += '<div style="background: #f8fafc; padding: 1.5rem; min-height: 100px;"></div>';
                        }
                        
                        // Fill actual days
                        for (let i = 1; i <= daysInMonth; i++) {
                            const dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(i).padStart(2, '0');
                            html += '<div style="background: white; padding: 1.5rem; min-height: 100px; text-align: left; font-size: 1.1rem; color: #64748b; cursor: pointer;" onclick="document.querySelector(\'input[name=from_date]\').value=\''+dateStr+'\'; document.querySelector(\'input[name=to_date]\').value=\''+dateStr+'\';">' + i + '</div>';
                        }
                        
                        // Fill remaining slots
                        const totalSlots = firstDay + daysInMonth;
                        const remaining = (Math.ceil(totalSlots / 7) * 7) - totalSlots;
                        for (let i = 0; i < remaining; i++) {
                            html += '<div style="background: #f8fafc; padding: 1.5rem; min-height: 100px;"></div>';
                        }
                        
                        document.getElementById('realCalendarGridBulk').innerHTML = html;
                    }
                    document.addEventListener('DOMContentLoaded', renderRealCalendarBulk);
                </script>
                <div id="realCalendarGridBulk" style="display: contents;"></div>
    
            </div>
        </div>

        <!-- Right Side: Sidebar -->
        <div style="width: 350px; background: #fff; padding: 1.5rem; display: flex; flex-direction: column;">
            <div style="text-align: right; margin-bottom: 1.5rem;">
                <button onclick="closeBulkModal()" style="background: transparent; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b;">&times;</button>
            </div>
            
            <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; font-weight: 700;">1 date selected</h3>
            
            <form id="bulkSpecialDateForm" style="flex: 1; display: flex; flex-direction: column; gap: 1.5rem;">
                @csrf
                <div>
                    <label style="font-size: 0.875rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.5rem;">Select Properties <span style="color:#ef4444">*</span></label>
                    <select name="property_ids[]" multiple required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; background: #f8fafc; height: 120px; font-size: 0.875rem;">
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}">{{ $prop->name }}</option>
                        @endforeach
                    </select>
                    <span style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem; display: block;">Hold Ctrl (or Cmd on Mac) to select multiple.</span>
                </div>
                
                <div>
                    <label style="font-size: 0.875rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.5rem;">Start date</label>
                    <input type="date" name="from_date" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; background: #f8fafc;">
                </div>
                
                <div>
                    <label style="font-size: 0.875rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.5rem;">End date</label>
                    <input type="date" name="to_date" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; background: #f8fafc;">
                </div>

                <div style="border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                    <label style="font-size: 0.875rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.75rem;">Advanced date selection</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        <label style="font-size: 0.875rem;"><input type="checkbox" name="days[]" value="1" checked> Mon</label>
                        <label style="font-size: 0.875rem;"><input type="checkbox" name="days[]" value="2" checked> Tue</label>
                        <label style="font-size: 0.875rem;"><input type="checkbox" name="days[]" value="3" checked> Wed</label>
                        <label style="font-size: 0.875rem;"><input type="checkbox" name="days[]" value="4" checked> Thu</label>
                        <label style="font-size: 0.875rem;"><input type="checkbox" name="days[]" value="5" checked> Fri</label>
                        <label style="font-size: 0.875rem;"><input type="checkbox" name="days[]" value="6" checked> Sat</label>
                        <label style="font-size: 0.875rem;"><input type="checkbox" name="days[]" value="0" checked> Sun</label>
                    </div>
                </div>

                <div style="border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                    <label style="font-size: 0.875rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.75rem;">Open or close for bookings</label>
                    <div style="display: flex; gap: 1rem;">
                        <label style="font-size: 0.875rem;"><input type="radio" name="is_open" value="1" checked> Open</label>
                        <label style="font-size: 0.875rem;"><input type="radio" name="is_open" value="0"> Closed</label>
                    </div>
                </div>

                <div>
                    <label style="font-size: 0.875rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.5rem;">Price (INR)</label>
                    <input type="number" name="amount" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; background: #f8fafc;">
                </div>

                <div style="margin-top: auto; display: flex; gap: 1rem; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                    <button type="button" onclick="closeBulkModal()" style="flex: 1; padding: 0.75rem; background: #f1f5f9; color: #475569; font-weight: 600; border: 1px solid #cbd5e1; border-radius: 0.5rem; cursor: pointer;">Cancel</button>
                    <button type="submit" style="flex: 1; padding: 0.75rem; background: #3b82f6; color: white; font-weight: 600; border: none; border-radius: 0.5rem; cursor: pointer;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openBulkModal() {
        document.getElementById('listView').style.display = 'none';
        document.getElementById('calendarView').style.display = 'block';
    }

    function closeBulkModal() {
        document.getElementById('calendarView').style.display = 'none';
        document.getElementById('listView').style.display = 'block';
    }

    document.getElementById('bulkSpecialDateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route("admin.properties.bulk_special_dates") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Validation failed'));
            }
        })
        .catch(err => alert('Network error occurred.'));
    });
</script>
@endsection

