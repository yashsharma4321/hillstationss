@extends('layouts.admin')

@section('header', 'Edit Vendor')

@section('content')
<div style="max-width: 800px;">
    <div class="content-card">
        <form action="{{ route('admin.vendors.update', $vendor->id) }}" method="POST" style="padding: 2rem;">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="full-width" style="grid-column: span 2; border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 0.5rem;">
                    <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600;">Business Information</h3>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Business Name</label>
                    <input type="text" name="business_name" value="{{ old('business_name', $vendor->business_name) }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; outline: none;" required>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Business Email</label>
                    <input type="email" name="business_email" value="{{ old('business_email', $vendor->business_email) }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; outline: none;" required>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Business Phone</label>
                    <input type="text" name="business_phone" value="{{ old('business_phone', $vendor->business_phone) }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; outline: none;" required>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Amount / Set Fee</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $vendor->amount) }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; outline: none;">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Commission Rate (%)</label>
                    <input type="number" step="0.01" name="commission_rate" value="{{ old('commission_rate', $vendor->commission_rate) }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; outline: none;" required>
                </div>

                <div class="full-width" style="grid-column: span 2; border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-top: 1rem; margin-bottom: 0.5rem;">
                    <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600;">Bank Information</h3>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Bank / Account Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', optional($vendor->bankDetail)->bank_name) }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; outline: none;">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Account Number</label>
                    <input type="text" name="account_number" value="{{ old('account_number', optional($vendor->bankDetail)->account_number) }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; outline: none;">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">IFSC Code</label>
                    <input type="text" name="ifsc_code" value="{{ old('ifsc_code', optional($vendor->bankDetail)->ifsc_code) }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; outline: none;">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">UPI ID</label>
                    <input type="text" name="upi_id" value="{{ old('upi_id', optional($vendor->bankDetail)->upi_id) }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; outline: none;">
                </div>

                <div class="full-width" style="grid-column: span 2; border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-top: 1rem; margin-bottom: 0.5rem;">
                    <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600;">Account Settings</h3>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Approval Status</label>
                    <select name="is_approved" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; outline: none; background: white;">
                        <option value="1" {{ $vendor->is_approved ? 'selected' : '' }}>Approved</option>
                        <option value="0" {{ !$vendor->is_approved ? 'selected' : '' }}>Pending/Hidden</option>
                    </select>
                    <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.5rem;">Only approved vendors can list properties publicly.</p>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">KYC Status</label>
                    <select name="kyc_status" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; outline: none; background: white;">
                        <option value="pending" {{ $vendor->kyc_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $vendor->kyc_status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $vendor->kyc_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Overall Status</label>
                    <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; outline: none; background: white;">
                        <option value="active" {{ $vendor->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $vendor->status == 'inactive' ? 'selected' : '' }}>Inactive / Suspended</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                <a href="{{ route('admin.vendors.index') }}" style="padding: 0.75rem 1.5rem; background: #f1f5f9; color: #475569; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.875rem;">Cancel</a>
                <button type="submit" style="padding: 0.75rem 2rem; background: var(--primary); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
