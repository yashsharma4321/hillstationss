@extends('layouts.admin')

@section('header', 'Vendor Details')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem;">
    <!-- Main Content -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <!-- Vendor Profile Card -->
        <div class="content-card" style="padding: 2.5rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1.5rem;">
                    <div style="width: 80px; height: 80px; border-radius: 1.25rem; background: #f8fafc; border: 1.5px solid var(--border); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        @if($vendor->brand_logo)
                            <img src="{{ url(Storage::url($vendor->brand_logo)) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <span style="font-size: 1.75rem; font-weight: 700; color: #cbd5e1;">{{ substr($vendor->business_name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div>
                        <h2 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">{{ $vendor->business_name }}</h2>
                        <p style="color: #64748b; font-size: 1rem; font-weight: 500;">Owner: <span style="color: var(--primary); font-weight: 600;">{{ $vendor->user->name }}</span></p>
                    </div>
                </div>
                <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Edit Profile
                </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem;">
                <div style="background: #f8fafc; padding: 1.25rem; border-radius: 1rem; border: 1px solid var(--border);">
                    <span style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Business Email</span>
                    <span style="font-weight: 600; color: #1e293b; font-size: 0.9375rem;">{{ $vendor->business_email }}</span>
                </div>
                <div style="background: #f8fafc; padding: 1.25rem; border-radius: 1rem; border: 1px solid var(--border);">
                    <span style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Business Phone</span>
                    <span style="font-weight: 600; color: #1e293b; font-size: 0.9375rem;">{{ $vendor->business_phone }}</span>
                </div>
                <div style="background: #ecfdf5; padding: 1.25rem; border-radius: 1rem; border: 1px solid #d1fae5;">
                    <span style="display: block; font-size: 0.75rem; color: #065f46; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Commission Rate</span>
                    <span style="font-weight: 800; color: #059669; font-size: 1.25rem;">{{ $vendor->commission_rate }}%</span>
                </div>
                <div style="grid-column: span 3; background: #f8fafc; padding: 1.25rem; border-radius: 1rem; border: 1px solid var(--border); display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 40px; height: 40px; background: white; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <div>
                        <span style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 0.125rem;">Registered Address</span>
                        <span style="font-weight: 600; color: #1e293b;">{{ $vendor->address }}, {{ $vendor->city }}, {{ $vendor->state }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Properties Section -->
        <div class="content-card">
            <div class="card-header" style="padding: 1.5rem 2rem;">
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.25rem;">Managed Properties</h3>
                    <p style="font-size: 0.8125rem; color: #64748b; margin: 0;">Total {{ $vendor->properties->count() }} active and pending listings</p>
                </div>
                <a href="{{ route('admin.properties.index', ['vendor_id' => $vendor->id]) }}" style="color: var(--primary); font-size: 0.875rem; font-weight: 700; text-decoration: none; padding: 0.5rem 1rem; background: var(--primary-light); border-radius: 0.75rem;">View All Listing</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 2rem;">PROPERTY NAME</th>
                        <th>LOCATION</th>
                        <th>SPECS</th>
                        <th>STATUS</th>
                        <th style="padding-right: 2rem;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendor->properties as $property)
                    <tr>
                        <td style="padding-left: 2rem; font-weight: 700; color: #1e293b;">{{ $property->name }}</td>
                        <td>{{ $property->city }}</td>
                        <td>
                            <div style="display: flex; gap: 0.5rem; color: #64748b; font-size: 0.8125rem; font-weight: 500;">
                                <span>{{ $property->total_bedrooms }} BHK</span>
                                <span>•</span>
                                <span>{{ $property->max_guests }} Guests</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $property->status == 'active' ? 'badge-success' : 'badge-pending' }}" style="font-size: 0.75rem; letter-spacing: 0.025em;">
                                {{ strtoupper($property->status) }}
                            </span>
                        </td>
                        <td style="padding-right: 2rem;">
                            <a href="{{ route('admin.properties.show', $property->id) }}" style="color: var(--primary); font-weight: 700; text-decoration: none; font-size: 0.8125rem;">MANAGE</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 4rem; color: #94a3b8;">
                            <div style="margin-bottom: 1rem;"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></div>
                            No properties found for this vendor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="content-card" style="padding: 1.5rem;">
            <h4 style="margin: 0 0 1rem 0; font-size: 0.875rem; text-transform: uppercase; color: #94a3b8;">Account Status</h4>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <span style="display: block; font-size: 0.8125rem; color: #64748b; margin-bottom: 0.25rem;">Approval Status</span>
                    @if($vendor->is_approved)
                        <span style="background: #ecfdf5; color: #059669; padding: 0.25rem 0.5rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700;">APPROVED</span>
                    @else
                        <span style="background: #fff7ed; color: #d97706; padding: 0.25rem 0.5rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700;">PENDING</span>
                    @endif
                </div>
                <div>
                    <span style="display: block; font-size: 0.8125rem; color: #64748b; margin-bottom: 0.25rem;">KYC Status</span>
                    <span style="background: #f1f5f9; color: #475569; padding: 0.25rem 0.5rem; border-radius: 99px; font-size: 0.75rem; font-weight: 700;">{{ strtoupper($vendor->kyc_status) }}</span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.8125rem; color: #64748b; margin-bottom: 0.25rem;">User Account</span>
                    <span style="font-weight: 600;">#{{ $vendor->user_id }} - {{ $vendor->user->role }}</span>
                </div>
            </div>
        </div>

        <div class="content-card" style="padding: 1.5rem; background: #0f172a; color: white;">
            <h4 style="margin: 0 0 1rem 0; font-size: 0.875rem; text-transform: uppercase; color: #94a3b8;">Financial Info</h4>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <span style="display: block; font-size: 0.8125rem; color: #94a3b8; margin-bottom: 0.25rem;">Wallet Balance</span>
                    <span style="font-size: 1.25rem; font-weight: 700;">₹{{ number_format($vendor->wallet->balance ?? 0) }}</span>
                </div>
                <div style="padding-top: 1rem; border-top: 1px solid #1e293b;">
                    <span style="display: block; font-size: 0.8125rem; color: #94a3b8; margin-bottom: 0.25rem;">Bank Account</span>
                    @if($vendor->bankDetail)
                        <span style="font-weight: 600; display: block;">{{ $vendor->bankDetail->bank_name }}</span>
                        <span style="font-size: 0.75rem; color: #94a3b8;">**** **** {{ substr($vendor->bankDetail->account_number, -4) }}</span>
                    @else
                        <span style="font-size: 0.75rem; color: #ef4444;">Not set up</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
