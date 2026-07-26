@extends('layouts.admin')

@section('header', 'Amenities Manager')

@section('styles')
<style>
    .amenity-card:hover { background-color: #f8fafc; }
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
        color: #64748b;
    }
    .action-btn:hover { background: #f1f5f9; border-color: var(--primary); color: var(--primary); }
    .action-btn.delete:hover { border-color: var(--danger); color: var(--danger); }
</style>
@endsection

@section('content')
<div class="content-card">
    <div class="card-header" style="background: white;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">Amenities</h2>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">Manage features and services offered at your properties.</p>
        </div>
        <a href="{{ route('admin.amenities.create') }}" 
           style="background: var(--primary); color: white; padding: 0.75rem 1.5rem; border-radius: 0.625rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2); transition: all 0.2s;">
           + New Amenity
        </a>
    </div>

    @if(session('success'))
        <div style="margin: 1.5rem; padding: 1rem; background: #ecfdf5; color: #065f46; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.75rem; border: 1px solid #10b981;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span style="font-size: 0.875rem; font-weight: 500;">{{ session('success') }}</span>
        </div>
    @endif

    <div style="padding: 0 1.5rem 1.5rem 1.5rem;">
        <div style="overflow-x: auto; border: 1px solid var(--border); border-radius: 0.75rem;">
            <table style="min-width: 800px;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="border-bottom: 1px solid var(--border); width: 80px;">ID</th>
                        <th style="border-bottom: 1px solid var(--border);">Amenity Info</th>
                        <th style="border-bottom: 1px solid var(--border);">Category</th>
                        <th style="border-bottom: 1px solid var(--border);">Created</th>
                        <th style="border-bottom: 1px solid var(--border); text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($amenities as $amenity)
                        <tr class="amenity-card" style="transition: background 0.2s;">
                            <td style="padding: 1.25rem 1.5rem; font-weight: 600; color: #94a3b8;">#{{ $amenity->id }}</td>
                            <td style="padding: 1.25rem 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="width: 40px; height: 40px; background: #f8fafc; border: 1px dashed #e2e8f0; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        @if($amenity->icon)
                                            <img src="{{ Storage::url($amenity->icon) }}" style="width: 24px; height: 24px; object-fit: contain;">
                                        @else
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #cbd5e1;"><path d="M12 2L2 7l10 5l10 -5l-10 -5zM2 17l10 5l10 -5M2 12l10 5l10 -5"></path></svg>
                                        @endif
                                    </div>
                                    <div style="font-weight: 700; color: #1e293b;">{{ $amenity->name }}</div>
                                </div>
                            </td>
                            <td style="padding: 1.25rem 1.5rem;">
                                <span style="font-size: 0.75rem; background: #f1f5f9; color: #475569; padding: 0.2rem 0.6rem; border-radius: 0.375rem; border: 1px solid #e2e8f0;">{{ $amenity->category ?: 'General' }}</span>
                            </td>
                            <td style="padding: 1.25rem 1.5rem; color: #475569; font-size: 0.875rem;">
                                {{ $amenity->created_at->format('M d, Y') }}
                            </td>
                            <td style="padding: 1.25rem 1.5rem; text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                    <a href="{{ route('admin.amenities.edit', $amenity) }}" class="action-btn" title="Edit Amenity">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.amenities.destroy', $amenity) }}" method="POST" onsubmit="return confirm('Delete this amenity?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete Amenity">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 4rem 1.5rem;">
                                <p style="color: var(--text-muted);">No amenities found.</p>
                                <a href="{{ route('admin.amenities.create') }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">Add your first one</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($amenities->hasPages())
        <div style="margin-top: 1.5rem;">
            {{ $amenities->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

