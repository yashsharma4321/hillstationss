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
        <a href="{{ route('admin.properties.create') }}" 
           style="background: var(--primary); color: white; padding: 0.75rem 1.5rem; border-radius: 0.625rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2); transition: all 0.2s;"
           onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 8px -1px rgba(99, 102, 241, 0.3)';"
           onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px -1px rgba(99, 102, 241, 0.2)';">
           + New Property
        </a>
    </div>

    @if(session('success'))
        <div style="margin: 1.5rem; padding: 1rem; background: #ecfdf5; color: #065f46; border-radius: 0.5rem; border: 1px solid #a7f3d0; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span style="font-size: 0.875rem; font-weight: 500;">{{ session('success') }}</span>
        </div>
    @endif

    <div style="padding: 0 1.5rem 1.5rem 1.5rem;">
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
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                    {{-- Rooms Button --}}
                                    <a href="{{ route('admin.properties.rooms.index', $property) }}"
                                       class="action-btn" title="Manage Rooms"
                                       style="width:auto; padding:0 0.6rem; gap:0.3rem; font-size:0.72rem; font-weight:700; color:#4338ca; border-color:#c7d2fe; background:#eef2ff;">
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
                                <a href="{{ route('admin.properties.create') }}" style="display: inline-block; margin-top: 1.5rem; color: var(--primary); font-weight: 600; text-decoration: none;">+ Add your first property</a>
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
@endsection

