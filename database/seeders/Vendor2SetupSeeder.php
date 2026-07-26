<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\VendorWallet;
use App\Models\Transaction;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Coupon;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Vendor2SetupSeeder extends Seeder
{
    public function run(): void
    {
        $vendor = Vendor::find(2);
        if (!$vendor) {
            $this->command->error('Vendor 2 not found!');
            return;
        }

        // ─── 1. Set Commission Rate to 10% ───────────────────────────────
        $vendor->update(['commission_rate' => 10]);
        $this->command->info("✅ Vendor 2 ({$vendor->business_name}) commission_rate set to 10%");

        // ─── 2. Fix existing bookings that have vendor_amount = 0 ────────
        $zeroAmountBookings = Booking::where('vendor_id', 2)
            ->where('vendor_amount', 0)
            ->where('total_amount', '>', 0)
            ->get();

        foreach ($zeroAmountBookings as $booking) {
            $commission = round($booking->total_amount * 0.10, 2);
            $vendorAmt  = round($booking->total_amount - $commission, 2);
            $booking->update([
                'commission_percentage' => 10,
                'commission_amount'     => $commission,
                'vendor_amount'         => $vendorAmt,
            ]);
            $this->command->info("  Fixed booking {$booking->booking_number}: vendor_amount = ₹{$vendorAmt}");
        }

        // ─── 3. Sync Vendor Wallet from ALL confirmed+paid bookings ──────
        $totalVendorEarnings = Booking::where('vendor_id', 2)
            ->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->sum('vendor_amount');

        // Calculate refund deductions (cancelled bookings)
        $totalRefundDeductions = Transaction::where('vendor_id', 2)
            ->where('type', 'debit')
            ->where('category', 'refund')
            ->sum('amount');

        $correctBalance = $totalVendorEarnings - $totalRefundDeductions;

        $wallet = VendorWallet::firstOrCreate(
            ['vendor_id' => 2],
            ['balance' => 0, 'total_earned' => 0]
        );

        $oldBalance = $wallet->balance;
        $wallet->update([
            'balance'      => $correctBalance,
            'total_earned' => $totalVendorEarnings,
        ]);

        $this->command->info("✅ Wallet synced: balance ₹{$oldBalance} → ₹{$correctBalance} (total_earned: ₹{$totalVendorEarnings})");

        // ─── 4. Create transaction records for bookings that don't have one ─
        $confirmedBookings = Booking::where('vendor_id', 2)
            ->where('status', 'confirmed')
            ->where('payment_status', 'paid')
            ->where('vendor_amount', '>', 0)
            ->get();

        $existingTxnBookingIds = Transaction::where('vendor_id', 2)
            ->where('category', 'booking_earning')
            ->pluck('reference_id')
            ->toArray();

        $runningBalance = 0;
        foreach ($confirmedBookings as $booking) {
            if (!in_array($booking->id, $existingTxnBookingIds)) {
                $runningBalance += $booking->vendor_amount;
                Transaction::create([
                    'vendor_id'     => 2,
                    'amount'        => $booking->vendor_amount,
                    'type'          => 'credit',
                    'category'      => 'booking_earning',
                    'description'   => "Earning from booking #{$booking->booking_number}",
                    'reference_id'  => $booking->id,
                    'balance_after' => $runningBalance,
                ]);
                $this->command->info("  Created transaction for {$booking->booking_number}: +₹{$booking->vendor_amount}");
            }
        }

        // ─── 5. Seed a NEW booking for vendor 2 ─────────────────────────
        $property = Property::where('vendor_id', 2)->where('id', 1)->first(); // Hilltop
        if (!$property) {
            $property = Property::where('vendor_id', 2)->first();
        }

        $baseAmount        = 8000;
        $commissionPct     = 10;
        $commissionAmount  = round($baseAmount * $commissionPct / 100, 2); // 800
        $vendorAmount      = round($baseAmount - $commissionAmount, 2);    // 7200
        $gstAmount         = round($baseAmount * 0.18, 2);                 // 1440
        $finalAmount       = $baseAmount + $gstAmount;                     // 9440

        $newBooking = Booking::create([
            'booking_number'        => 'BST-' . strtoupper(Str::random(10)),
            'customer_id'           => 8, // user@example.com
            'vendor_id'             => 2,
            'property_id'           => $property->id,
            'check_in'              => Carbon::now()->addDays(15)->toDateString(),
            'check_out'             => Carbon::now()->addDays(18)->toDateString(),
            'guest_count'           => 4,
            'adults'                => 3,
            'children'              => 1,
            'total_amount'          => $baseAmount,
            'discount_amount'       => 0,
            'gst_amount'            => $gstAmount,
            'commission_percentage' => $commissionPct,
            'commission_amount'     => $commissionAmount,
            'vendor_amount'         => $vendorAmount,
            'final_amount'          => $finalAmount,
            'status'                => 'confirmed',
            'payment_status'        => 'paid',
            'razorpay_payment_id'   => 'pay_seed_' . Str::random(12),
            'razorpay_order_id'     => 'order_seed_' . Str::random(10),
            'razorpay_signature'    => Str::random(32),
        ]);

        // Credit wallet for the new booking
        $wallet->increment('balance', $vendorAmount);
        $wallet->increment('total_earned', $vendorAmount);
        $wallet->refresh();

        Transaction::create([
            'vendor_id'     => 2,
            'amount'        => $vendorAmount,
            'type'          => 'credit',
            'category'      => 'booking_earning',
            'description'   => "Earning from booking #{$newBooking->booking_number}",
            'reference_id'  => $newBooking->id,
            'balance_after' => $wallet->balance,
        ]);

        $this->command->info("✅ New booking seeded: {$newBooking->booking_number} | ₹{$baseAmount} total | ₹{$vendorAmount} vendor | ₹{$commissionAmount} commission");

        // ─── 6. Create Coupon for customer user@example.com (user_id: 8) ─
        $coupon = Coupon::create([
            'code'          => 'WELCOME' . strtoupper(Str::random(4)),
            'type'          => 'percentage',
            'value'         => 15,
            'min_purchase'  => 2000,
            'expires_at'    => Carbon::now()->addMonths(3),
            'usage_limit'   => 3,
            'used_count'    => 0,
            'is_active'     => true,
            'is_global'     => false,
            'description'   => 'Special 15% discount coupon for loyal customer',
            'vendor_id'     => $vendor->user_id, // vendor's user_id
            'property_id'   => $property->id,
        ]);

        $this->command->info("✅ Coupon created: {$coupon->code} (15% off, min ₹2000, for property: {$property->name})");

        // ─── 7. Create a second property-specific coupon ─────────────────
        $coupon2 = Coupon::create([
            'code'          => 'HILLTOP500',
            'type'          => 'flat',
            'value'         => 500,
            'min_purchase'  => 3000,
            'expires_at'    => Carbon::now()->addMonths(6),
            'usage_limit'   => 10,
            'used_count'    => 0,
            'is_active'     => true,
            'is_global'     => false,
            'description'   => '₹500 flat off on Hilltop property bookings',
            'vendor_id'     => $vendor->user_id,
            'property_id'   => 1, // Hilltop
        ]);

        $this->command->info("✅ Coupon created: {$coupon2->code} (₹500 flat off, min ₹3000, for Hilltop)");

        // ─── Summary ────────────────────────────────────────────────────
        $wallet->refresh();
        $this->command->newLine();
        $this->command->info("═══════════════════════════════════════");
        $this->command->info("  VENDOR 2 SETUP COMPLETE");
        $this->command->info("  Commission Rate: 10%");
        $this->command->info("  Wallet Balance:  ₹" . number_format($wallet->balance, 2));
        $this->command->info("  Total Earned:    ₹" . number_format($wallet->total_earned, 2));
        $this->command->info("  Total Bookings:  " . Booking::where('vendor_id', 2)->count());
        $this->command->info("  Transactions:    " . Transaction::where('vendor_id', 2)->count());
        $this->command->info("  Coupons:         {$coupon->code}, {$coupon2->code}");
        $this->command->info("═══════════════════════════════════════");
    }
}
