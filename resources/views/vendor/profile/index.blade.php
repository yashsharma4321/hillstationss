@extends('layouts.vendor')

@section('header', 'My Profile')

@section('styles')
<style>
    .profile-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    @media (min-width: 992px) {
        .profile-container {
            grid-template-columns: 1fr 1fr;
        }
    }
    .profile-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .card-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }
    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--text-light);
    }
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.2s;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .btn-submit {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-top: 1rem;
    }
    .btn-submit:hover {
        background: var(--primary-dark);
    }
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
</style>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('vendor.profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="profile-container">
            <!-- Business Details -->
            <div class="profile-card">
                <div class="card-header">
                    <h2 class="card-title">Business Information</h2>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Business Name</label>
                    <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $vendor->business_name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Business Email</label>
                    <input type="email" name="business_email" class="form-control" value="{{ old('business_email', $vendor->business_email) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Business Phone</label>
                    <input type="text" name="business_phone" class="form-control" value="{{ old('business_phone', $vendor->business_phone) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $vendor->city) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="{{ old('state', $vendor->state) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Full Address</label>
                    <textarea name="address" class="form-control" rows="3">{{ old('address', $vendor->address) }}</textarea>
                </div>
            </div>

            <!-- Bank Details -->
            <div class="profile-card">
                <div class="card-header">
                    <h2 class="card-title">Bank & Payout Details</h2>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Account Name / Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', optional($vendor->bankDetail)->bank_name) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="account_number" class="form-control" value="{{ old('account_number', optional($vendor->bankDetail)->account_number) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">IFSC Code</label>
                    <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', optional($vendor->bankDetail)->ifsc_code) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">UPI ID (Optional)</label>
                    <input type="text" name="upi_id" class="form-control" value="{{ old('upi_id', optional($vendor->bankDetail)->upi_id) }}">
                </div>

                <button type="submit" class="btn-submit w-full">Save Profile & Bank Details</button>
            </div>
        </div>
    </form>
</div>
@endsection
