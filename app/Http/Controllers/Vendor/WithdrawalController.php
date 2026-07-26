<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\VendorWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;
        $wallet = $vendor->wallet()->firstOrCreate([], ['balance' => 0, 'total_earned' => 0]);
        $requests = WithdrawalRequest::where('vendor_id', $vendor->id)->latest()->paginate(10);
        
        return view('vendor.withdrawals.index', compact('wallet', 'requests'));
    }

    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $wallet = $vendor->wallet;

        $request->validate([
            'amount' => 'required|numeric|min:500|max:' . ($wallet->balance ?? 0),
        ], [
            'amount.max' => 'You do not have enough balance to withdraw this amount.',
            'amount.min' => 'Minimum withdrawal amount is ₹500.'
        ]);

        // Get bank details snapshot
        $bankDetails = $vendor->bankDetail;
        if (!$bankDetails) {
            return redirect()->back()->with('error', 'Please update your bank details first.');
        }

        WithdrawalRequest::create([
            'vendor_id' => $vendor->id,
            'amount' => $request->amount,
            'status' => 'pending',
            'bank_details' => [
                'bank_name' => $bankDetails->bank_name,
                'account_number' => $bankDetails->account_number,
                'ifsc_code' => $bankDetails->ifsc_code,
                'account_holder' => $bankDetails->account_holder_name,
            ]
        ]);

        return redirect()->route('vendor.withdrawals.index')->with('success', 'Withdrawal request submitted successfully.');
    }
}
