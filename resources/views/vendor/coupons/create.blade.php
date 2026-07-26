@extends('layouts.vendor')

@section('header', 'Create Coupon')

@section('content')
<div style="max-width: 800px;">
    <div style="background: white; border: 1px solid var(--border); border-radius: 0.75rem; overflow: hidden;">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border);">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-main);">Coupon Details</h3>
        </div>

        <form action="{{ route('vendor.coupons.store') }}" method="POST" style="padding: 2rem;">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Select Property <span style="color:red">*</span></label>
                    <select name="property_id" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; background: white;">
                        <option value="">-- Choose a Property --</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Select Customer (Optional)</label>
                    <select name="user_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; background: white;">
                        <option value="">-- All Customers --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('user_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Coupon Code <span style="color:red">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required 
                           style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; text-transform: uppercase;"
                           placeholder="e.g. WELCOME100">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Discount Type <span style="color:red">*</span></label>
                    <select name="type" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; background: white;">
                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Discount Value <span style="color:red">*</span></label>
                    <input type="number" name="value" value="{{ old('value') }}" required step="0.01"
                           style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;"
                           placeholder="e.g. 500 or 10">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Min. Purchase Required (₹)</label>
                    <input type="number" name="min_purchase" value="{{ old('min_purchase', 0) }}" step="0.01"
                           style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Usage Limit (Total)</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}"
                           style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;"
                           placeholder="Leave empty for unlimited">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Expiry Date</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                           style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Description / Help Text</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;" 
                          placeholder="e.g. Get ₹500 off on your first booking of ₹5000+">{{ old('description') }}</textarea>
            </div>

            <div style="display: flex; gap: 2rem; margin-bottom: 2rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 600; font-size: 0.875rem;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                    <span>Is Active</span>
                </label>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('vendor.coupons.index') }}" style="padding: 0.75rem 1.5rem; background: #f1f5f9; color: #475569; border-radius: 0.5rem; text-decoration: none; font-weight: 600;">Cancel</a>
                <button type="submit" style="padding: 0.75rem 2.5rem; background: var(--primary); color: white; border: none; border-radius: 0.5rem; font-weight: 700; cursor: pointer;">Save Coupon</button>
            </div>
        </form>
    </div>
</div>
@endsection
