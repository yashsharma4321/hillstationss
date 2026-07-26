@extends('layouts.admin')

@section('header', 'Vendor Management')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 style="font-size: 1.125rem; font-weight: 600;">All Vendors</h2>
        <button style="padding: 0.5rem 1rem; background: var(--primary); color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">Add New Vendor</button>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Business Name</th>
                <th>Owner</th>
                <th>KYC Status</th>
                <th>Commission</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vendors as $vendor)
            <tr>
                <td>#{{ $vendor->id }}</td>
                <td style="font-weight: 600;">{{ $vendor->business_name }}</td>
                <td>{{ $vendor->user->name }}</td>
                <td>
                    <span class="badge {{ $vendor->kyc_status == 'approved' ? 'badge-success' : 'badge-pending' }}">
                        {{ strtoupper($vendor->kyc_status) }}
                    </span>
                </td>
                <td>{{ $vendor->commission_rate }}%</td>
                <td>
                    <span class="badge {{ $vendor->status == 'active' ? 'badge-success' : 'badge-pending' }}">
                        {{ strtoupper($vendor->status) }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 10px;">
                        <a href="{{ route('admin.vendors.show', $vendor->id) }}" style="color: var(--primary); font-weight: 600; text-decoration: none; font-size: 0.75rem;">VIEW</a>
                        <a href="{{ route('admin.vendors.edit', $vendor->id) }}" style="color: #64748b; font-weight: 600; text-decoration: none; font-size: 0.75rem;">EDIT</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                    No vendors found in the system.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding: 1rem;">
        {{ $vendors->links() }}
    </div>
</div>
@endsection
