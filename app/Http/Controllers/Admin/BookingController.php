<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Vendor;
use App\Models\User;
use App\Models\VendorWallet;
use App\Models\Transaction;
use App\Models\AccountHead;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * List bookings with search, filter (default: paid)
     */
    public function index(Request $request)
    {
        $query = Booking::with(['customer', 'vendor', 'property']);

        // Default: show only paid bookings
        $paymentStatus = $request->get('payment_status', 'paid');

        if ($paymentStatus !== 'all') {
            $query->where('payment_status', $paymentStatus);
        }

        // Search by customer name / email / phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhere('booking_number', 'like', "%{$search}%");
            });
        }

        // Filter by booking status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range (check_in)
        if ($request->filled('date_from')) {
            $query->whereDate('check_in', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('check_in', '<=', $request->date_to);
        }

        $bookings = $query->latest()->paginate(20)->withQueryString();

        $properties = Property::orderBy('name')->get();
        $vendors    = Vendor::orderBy('business_name')->get();

        return view('admin.bookings.index', compact('bookings', 'properties', 'vendors'));
    }

    /**
     * Show booking details
     */
    public function show(Booking $booking)
    {
        $booking->load(['customer', 'vendor', 'property', 'coupon']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Edit booking form
     */
    public function edit(Booking $booking)
    {
        $booking->load(['customer', 'vendor', 'property']);
        $properties = Property::orderBy('name')->get();
        $vendors    = Vendor::orderBy('business_name')->get();
        $customers  = User::where('role', 'customer')->orderBy('name')->get();
        return view('admin.bookings.edit', compact('booking', 'properties', 'vendors', 'customers'));
    }

    /**
     * Update booking
     */
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'check_in'    => 'required|date',
            'check_out'   => 'required|date|after:check_in',
            'guest_count' => 'required|integer|min:1',
            'status'      => 'required|in:pending,confirmed,cancelled',
            'payment_status' => 'required|in:pending,paid,refunded',
            'final_amount'   => 'required|numeric|min:0',
            'vendor_amount'  => 'required|numeric|min:0',
            'commission_amount' => 'required|numeric|min:0',
            'gst_amount'     => 'nullable|numeric|min:0',
        ]);

        $booking->update([
            'check_in'          => $request->check_in,
            'check_out'         => $request->check_out,
            'guest_count'       => $request->guest_count,
            'status'            => $request->status,
            'payment_status'    => $request->payment_status,
            'final_amount'      => $request->final_amount,
            'vendor_amount'     => $request->vendor_amount,
            'commission_amount' => $request->commission_amount,
            'gst_amount'        => $request->gst_amount ?? 0,
        ]);

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Create booking form
     */
    public function create()
    {
        $properties = Property::orderBy('name')->get();
        $vendors    = Vendor::orderBy('business_name')->get();
        $customers  = User::where('role', 'customer')->orderBy('name')->get();
        return view('admin.bookings.create', compact('properties', 'vendors', 'customers'));
    }

    /**
     * Store new booking (admin-created manual booking)
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required|exists:users,id',
            'property_id'    => 'required|exists:properties,id',
            'vendor_id'      => 'required|exists:vendors,id',
            'check_in'       => 'required|date',
            'check_out'      => 'required|date|after:check_in',
            'guest_count'    => 'required|integer|min:1',
            'total_amount'   => 'required|numeric|min:0',
            'gst_amount'     => 'nullable|numeric|min:0',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'payment_status' => 'required|in:pending,paid',
            'status'         => 'required|in:pending,confirmed',
        ]);

        $totalAmount      = $request->total_amount;
        $gstAmount        = $request->gst_amount ?? 0;
        $commissionPct    = $request->commission_percentage;
        $commissionAmount = round(($totalAmount * $commissionPct) / 100, 2);
        $vendorAmount     = round($totalAmount - $commissionAmount, 2);
        $finalAmount      = $totalAmount + $gstAmount;

        $booking = Booking::create([
            'booking_number'       => 'BST-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'customer_id'          => $request->customer_id,
            'property_id'          => $request->property_id,
            'vendor_id'            => $request->vendor_id,
            'check_in'             => $request->check_in,
            'check_out'            => $request->check_out,
            'guest_count'          => $request->guest_count,
            'total_amount'         => $totalAmount,
            'gst_amount'           => $gstAmount,
            'commission_percentage'=> $commissionPct,
            'commission_amount'    => $commissionAmount,
            'vendor_amount'        => $vendorAmount,
            'discount_amount'      => 0,
            'final_amount'         => $finalAmount,
            'status'               => $request->status,
            'payment_status'       => $request->payment_status,
        ]);

        // If paid, record accounting journal entry AND credit vendor wallet
        if ($request->payment_status === 'paid') {
            $this->recordBookingJournal($booking);

            // Credit vendor wallet
            if ($booking->vendor_id && $vendorAmount > 0) {
                $wallet = VendorWallet::firstOrCreate(
                    ['vendor_id' => $booking->vendor_id],
                    ['balance' => 0, 'total_earned' => 0]
                );

                $wallet->increment('balance', $vendorAmount);
                $wallet->increment('total_earned', $vendorAmount);
                $wallet->refresh();

                Transaction::create([
                    'vendor_id'      => $booking->vendor_id,
                    'amount'         => $vendorAmount,
                    'type'           => 'credit',
                    'category'       => 'booking_earning',
                    'description'    => "Earning from booking #{$booking->booking_number}",
                    'reference_id'   => $booking->id,
                    'balance_after'  => $wallet->balance,
                ]);
            }
        }

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking created successfully (ID: ' . $booking->booking_number . ').');
    }

    /**
     * Cancel booking and refund — deduct from vendor wallet, reverse accounting & GST
     */
    public function cancelAndRefund(Request $request, Booking $booking)
    {
        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('error', 'Booking is already cancelled.');
        }

        return DB::transaction(function () use ($booking) {
            // Apply Cancellation Rules
            $property = $booking->property;
            $checkIn = Carbon::parse($booking->check_in);
            $daysUntilCheckIn = Carbon::now()->diffInDays($checkIn, false);
            
            $deductionPercentage = 0;
            if ($daysUntilCheckIn > 0 && $property->cancellationRules) {
                // Find applicable rule: closest rule where days_before >= daysUntilCheckIn
                $applicableRule = $property->cancellationRules()
                    ->where('days_before', '>=', $daysUntilCheckIn)
                    ->orderBy('days_before', 'asc')
                    ->first();
                
                if ($applicableRule) {
                    $deductionPercentage = $applicableRule->deduction_percentage;
                }
            }

            // Calculate exact refund amounts
            $vendorRefundDeduction = round(($booking->vendor_amount * $deductionPercentage) / 100, 2);
            $commissionRefundDeduction = round(($booking->commission_amount * $deductionPercentage) / 100, 2);
            
            $actualVendorRefund = $booking->vendor_amount - $vendorRefundDeduction;
            $actualCommissionRefund = $booking->commission_amount - $commissionRefundDeduction;

            // 1. Reverse Vendor Credit (Deduct from wallet)
            $wallet = VendorWallet::where('vendor_id', $booking->vendor_id)->first();
            if ($wallet && $actualVendorRefund > 0) {
                $wallet->decrement('balance', $actualVendorRefund);

                Transaction::create([
                    'vendor_id'      => $booking->vendor_id,
                    'amount'         => $actualVendorRefund,
                    'type'           => 'debit',
                    'category'       => 'refund',
                    'description'    => "Reversed earning for cancelled booking #{$booking->booking_number} (Deduction: {$deductionPercentage}%)",
                    'reference_id'   => $booking->id,
                    'balance_after'  => $wallet->balance,
                ]);
            }

            // 2. Reverse Admin Commission
            if ($actualCommissionRefund > 0) {
                $admin = User::where('role', 'admin')->first();
                Transaction::create([
                    'admin_id'      => $admin ? $admin->id : null,
                    'amount'        => $actualCommissionRefund,
                    'type'          => 'debit',
                    'category'      => 'commission_reversal',
                    'description'   => "Reversed commission for cancelled booking #{$booking->booking_number} (Deduction: {$deductionPercentage}%)",
                    'reference_id'  => $booking->id,
                    'balance_after' => 0,
                ]);
            }

            // 3. Update Booking Status
            $booking->update([
                'status'         => 'cancelled',
                'payment_status' => 'refunded',
            ]);

            // 4. Reverse Accounting Journal Entry (including GST)
            $originalJournal = JournalEntry::where('reference_type', Booking::class)
                ->where('reference_id', $booking->id)
                ->first();

            if ($originalJournal) {
                $reversal = JournalEntry::create([
                    'transaction_date' => now(),
                    'reference_type'   => Booking::class,
                    'reference_id'     => $booking->id,
                    'description'      => "Refund/Reversal for cancelled Booking #{$booking->booking_number}",
                ]);

                foreach ($originalJournal->lines as $line) {
                    $reversal->lines()->create([
                        'account_head_id' => $line->account_head_id,
                        'type'            => $line->type === 'debit' ? 'credit' : 'debit',
                        'amount'          => $line->amount,
                    ]);
                }
            } else {
                // Create reversal entries manually based on booking amounts
                $this->recordRefundJournal($booking, $deductionPercentage);
            }

            return redirect()->back()
                ->with('success', "Booking #{$booking->booking_number} cancelled. Deduction: {$deductionPercentage}%. Refund processed.");
        });
    }

    // ─── Private Helpers ───────────────────────────────────────────────────────

    private function recordBookingJournal(Booking $booking): void
    {
        $cashAccount       = AccountHead::where('name', 'like', '%Cash%')->orWhere('name', 'like', '%Bank%')->where('type', 'asset')->first();
        $revenueAccount    = AccountHead::where('type', 'revenue')->first();
        $commissionAccount = AccountHead::where('name', 'like', '%Commission%')->first();
        $gstAccount        = AccountHead::where('name', 'like', '%GST%')->orWhere('name', 'like', '%Tax%')->where('type', 'liability')->first();

        if (!$cashAccount || !$revenueAccount) {
            return; // Chart of accounts not set up yet
        }

        $journal = JournalEntry::create([
            'transaction_date' => now(),
            'reference_type'   => Booking::class,
            'reference_id'     => $booking->id,
            'description'      => "Booking #{$booking->booking_number} - Payment received",
        ]);

        // Dr. Cash/Bank (full final amount)
        $journal->lines()->create([
            'account_head_id' => $cashAccount->id,
            'type'            => 'debit',
            'amount'          => $booking->final_amount,
        ]);

        // Cr. Revenue (base amount excl. GST)
        $journal->lines()->create([
            'account_head_id' => $revenueAccount->id,
            'type'            => 'credit',
            'amount'          => $booking->total_amount,
        ]);

        // Cr. GST Payable
        if ($booking->gst_amount > 0 && $gstAccount) {
            $journal->lines()->create([
                'account_head_id' => $gstAccount->id,
                'type'            => 'credit',
                'amount'          => $booking->gst_amount,
            ]);
        }
    }

    private function recordRefundJournal(Booking $booking, $deductionPercentage = 0): void
    {
        $cashAccount    = AccountHead::where('name', 'like', '%Cash%')->orWhere('name', 'like', '%Bank%')->where('type', 'asset')->first();
        $revenueAccount = AccountHead::where('type', 'revenue')->first();
        $gstAccount     = AccountHead::where('name', 'like', '%GST%')->orWhere('name', 'like', '%Tax%')->where('type', 'liability')->first();

        if (!$cashAccount || !$revenueAccount) {
            return;
        }

        $refundJournal = JournalEntry::create([
            'transaction_date' => now(),
            'reference_type'   => Booking::class,
            'reference_id'     => $booking->id,
            'description'      => "Refund for cancelled Booking #{$booking->booking_number}",
        ]);

        $refundAmount = $booking->total_amount - round(($booking->total_amount * $deductionPercentage) / 100, 2);
        
        // Dr. Revenue (reversal of refunded amount)
        if ($refundAmount > 0) {
            $refundJournal->lines()->create([
                'account_head_id' => $revenueAccount->id,
                'type'            => 'debit',
                'amount'          => $refundAmount,
            ]);
        }

        // Dr. GST Payable (reversal)
        $gstRefund = $booking->gst_amount - round(($booking->gst_amount * $deductionPercentage) / 100, 2);
        if ($gstRefund > 0 && $gstAccount) {
            $refundJournal->lines()->create([
                'account_head_id' => $gstAccount->id,
                'type'            => 'debit',
                'amount'          => $gstRefund,
            ]);
        }

        // Cr. Cash/Bank (refund out)
        $finalRefundOut = $refundAmount + $gstRefund;
        if ($finalRefundOut > 0) {
            $refundJournal->lines()->create([
                'account_head_id' => $cashAccount->id,
                'type'            => 'credit',
                'amount'          => $finalRefundOut,
            ]);
        }
    }
}
