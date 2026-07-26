<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorBankDetail;

class ProfileController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;
        $vendor->load('bankDetail');
        return view('vendor.profile.index', compact('vendor'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_phone' => 'required|string|max:20',
            'business_email' => 'required|email|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'upi_id' => 'nullable|string|max:255',
        ]);

        $vendor = Auth::user()->vendor;
        
        $vendor->update([
            'business_name' => $request->business_name,
            'business_phone' => $request->business_phone,
            'business_email' => $request->business_email,
            'city' => $request->city,
            'state' => $request->state,
            'address' => $request->address,
        ]);

        $bankDetails = $vendor->bankDetail;
        if (!$bankDetails) {
            $bankDetails = new VendorBankDetail();
            $bankDetails->vendor_id = $vendor->id;
        }

        $bankDetails->account_number = $request->account_number;
        $bankDetails->bank_name = $request->bank_name;
        $bankDetails->ifsc_code = $request->ifsc_code;
        $bankDetails->upi_id = $request->upi_id;
        $bankDetails->save();

        return redirect()->route('vendor.profile')->with('success', 'Profile and Bank Details updated successfully.');
    }
}
