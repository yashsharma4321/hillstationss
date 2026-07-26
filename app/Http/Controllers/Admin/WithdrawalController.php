<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\Transaction;
use App\Models\VendorWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        $requests = WithdrawalRequest::with('vendor.user')->latest()->paginate(20);
        return view('admin.withdrawals.index', compact('requests'));
    }

    public function update(Request $request, WithdrawalRequest $withdrawal)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request, $withdrawal) {
            if ($request->status == 'approved' && $withdrawal->status != 'approved') {
                $wallet = VendorWallet::where('vendor_id', $withdrawal->vendor_id)->first();
                
                if ($wallet->balance < $withdrawal->amount) {
                    return redirect()->back()->with('error', 'Vendor has insufficient balance.');
                }

                // Deduct from wallet
                $wallet->decrement('balance', $withdrawal->amount);

                // Log Transaction
                Transaction::create([
                    'vendor_id' => $withdrawal->vendor_id,
                    'amount' => $withdrawal->amount,
                    'type' => 'debit',
                    'category' => 'payout',
                    'description' => 'Payout approved and processed.',
                    'reference_id' => $withdrawal->id,
                    'balance_after' => $wallet->balance
                ]);
            }

            $withdrawal->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes
            ]);

            return redirect()->back()->with('success', 'Withdrawal request ' . $request->status . ' successfully.');
        });
    }
}
