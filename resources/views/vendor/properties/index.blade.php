@extends('layouts.vendor')

@section('header', 'My Properties')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Listings</h2>
        <a href="{{ route('vendor.properties.create') }}" class="btn btn-primary">Add Property</a>
    </div>
    <div style="padding: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; background: #f8fafc;">
                    <th style="padding: 1rem;">Name</th>
                    <th style="padding: 1rem;">Category</th>
                    <th style="padding: 1rem;">Location</th>
                    <th style="padding: 1rem;">Status</th>
                    <th style="padding: 1rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $property)
                <tr style="border-top: 1px solid #e2e8f0;">
                    <td style="padding: 1rem;">{{ $property->name }}</td>
                    <td style="padding: 1rem;">{{ $property->category->name }}</td>
                    <td style="padding: 1rem;">{{ $property->city }}, {{ $property->country }}</td>
                    <td style="padding: 1rem;">
                        <span style="padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; background: #fef3c7; color: #92400e;">
                            {{ strtoupper($property->status) }}
                        </span>
                    </td>
                    <td style="padding: 1rem; display: flex; gap: 1rem; align-items: center;">
                         <a href="{{ route('vendor.properties.edit', $property) }}" style="color: var(--primary); font-weight: 600; text-decoration: none;">Edit</a>
                         <a href="{{ route('vendor.properties.calendar', $property) }}" style="color: #0284c7; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                             <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                             Calendar
                         </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 3rem; text-align: center; color: #64748b;">No properties found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 1rem;">{{ $properties->links() }}</div>
    </div>
</div>
@endsection
