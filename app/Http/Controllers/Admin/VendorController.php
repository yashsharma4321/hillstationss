<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of original vendors.
     */
    public function index()
    {
        $vendors = Vendor::with('user')->latest()->paginate(10);
        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Display the specified vendor.
     */
    public function show($id)
    {
        $vendor = Vendor::with(['user', 'properties', 'wallet', 'bankDetail'])->findOrFail($id);
        return view('admin.vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit($id)
    {
        $vendor = Vendor::with('user')->findOrFail($id);
        return view('admin.vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified vendor in storage.
     */
    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);
        
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_email' => 'required|email|max:255',
            'business_phone' => 'required|string|max:20',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'kyc_status' => 'required|in:pending,approved,rejected',
            'is_approved' => 'required|boolean',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'upi_id' => 'nullable|string|max:255',
        ]);

        $vendor->update($request->except(['bank_name', 'account_number', 'ifsc_code', 'upi_id']));

        // Update Bank Details
        $bankDetails = $vendor->bankDetail;
        if (!$bankDetails) {
            $bankDetails = new \App\Models\VendorBankDetail();
            $bankDetails->vendor_id = $vendor->id;
        }

        if ($request->filled('bank_name') || $request->filled('account_number') || $request->filled('ifsc_code') || $request->filled('upi_id')) {
            $bankDetails->bank_name = $request->bank_name;
            $bankDetails->account_number = $request->account_number;
            $bankDetails->ifsc_code = $request->ifsc_code;
            $bankDetails->upi_id = $request->upi_id;
            $bankDetails->save();
        }

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified vendor from storage.
     */
    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();
        return redirect()->route('admin.vendors.index')->with('success', 'Vendor deleted successfully.');
    }
}
