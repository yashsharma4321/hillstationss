<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Coupon;
use App\Models\Property;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::where('vendor_id', Auth::id())->latest()->paginate(10);
        return view('vendor.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $vendor = Auth::user()->vendor;
        $properties = $vendor ? Property::where('vendor_id', $vendor->id)->get() : collect();
        
        $customers = collect();
        if ($vendor) {
            $customerIds = \App\Models\Booking::where('vendor_id', $vendor->id)->distinct()->pluck('customer_id');
            $customers = \App\Models\User::whereIn('id', $customerIds)->orderBy('name')->get();
        }

        return view('vendor.coupons.create', compact('properties', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'property_id' => 'required|exists:properties,id',
            'min_purchase' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Ensure property belongs to vendor
        $vendor = Auth::user()->vendor;
        $property = Property::where('id', $request->property_id)->where('vendor_id', $vendor ? $vendor->id : 0)->firstOrFail();

        Coupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_purchase' => $request->min_purchase ?? 0,
            'expires_at' => $request->expires_at,
            'usage_limit' => $request->usage_limit,
            'is_global' => false,
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
            'vendor_id' => Auth::id(),
            'property_id' => $property->id,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('vendor.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function edit($id)
    {
        $coupon = Coupon::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();
        $vendor = Auth::user()->vendor;
        $properties = $vendor ? Property::where('vendor_id', $vendor->id)->get() : collect();
        
        $customers = collect();
        if ($vendor) {
            $customerIds = \App\Models\Booking::where('vendor_id', $vendor->id)->distinct()->pluck('customer_id');
            $customers = \App\Models\User::whereIn('id', $customerIds)->orderBy('name')->get();
        }

        return view('vendor.coupons.edit', compact('coupon', 'properties', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();

        $request->validate([
            'code' => 'required|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'property_id' => 'required|exists:properties,id',
            'min_purchase' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $vendor = Auth::user()->vendor;
        $property = Property::where('id', $request->property_id)->where('vendor_id', $vendor ? $vendor->id : 0)->firstOrFail();

        $coupon->update([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_purchase' => $request->min_purchase ?? 0,
            'expires_at' => $request->expires_at,
            'usage_limit' => $request->usage_limit,
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
            'property_id' => $property->id,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('vendor.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy($id)
    {
        $coupon = Coupon::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();
        $coupon->delete();
        return redirect()->route('vendor.coupons.index')->with('success', 'Coupon deleted successfully.');
    }
}
