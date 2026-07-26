<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Property;
use App\Models\Booking;
use App\Models\VendorWallet;
use App\Models\Transaction;
use App\Models\AccountHead;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Get or Create Customer
            $customer = User::firstOrCreate(
                ['email' => 'customer_fake@example.com'],
                [
                    'name' => 'Demo Customer',
                    'password' => Hash::make('password'),
                    'role' => 'customer'
                ]
            );

            // Get or Create Vendor
            $vendorUser = User::firstOrCreate(
                ['email' => 'vendor_fake@example.com'],
                [
                    'name' => 'Demo Vendor',
                    'password' => Hash::make('password'),
                    'role' => 'vendor'
                ]
            );

            $vendor = Vendor::firstOrCreate(
                ['user_id' => $vendorUser->id],
                [
                    'business_name' => 'Fake Resort & Spa',
                    'business_phone' => '9999999999',
                    'business_email' => 'vendor_fake@example.com',
                    'is_approved' => true,
                    'kyc_status' => 'verified',
                    'status' => 'active'
                ]
            );

            // Create Vendor Wallet if not exists
            $vendorWallet = VendorWallet::firstOrCreate(
                ['vendor_id' => $vendor->id],
                ['balance' => 0, 'total_earned' => 0]
            );

            $category = \App\Models\PropertyCategory::firstOrCreate(
                ['slug' => 'demo-category'],
                ['name' => 'Demo Category']
            );

            $destination = \App\Models\Destination::firstOrCreate(
                ['slug' => 'demo-destination'],
                ['name' => 'Demo Destination', 'status' => 'active']
            );

            // Get or Create Property
            $property = Property::firstOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'category_id' => $category->id,
                    'destination_id' => $destination->id,
                    'name' => 'Luxury Fake Villa',
                    'slug' => 'luxury-fake-villa',
                    'description' => 'A beautiful luxury villa for testing.',
                    'status' => 'approved',
                    'city' => 'Demo City',
                    'state' => 'Demo State',
                    'zip_code' => '123456',
                    'country' => 'India',
                    'address' => '123 Demo Street',
                    'latitude' => 28.6139,
                    'longitude' => 77.2090,
                ]
            );
            
            // Generate some random bookings in the past 30 days
            $cashHead = AccountHead::where('code', 'A-1002')->first();
            $vendorPayableHead = AccountHead::where('code', 'L-2001')->first();
            $commissionIncomeHead = AccountHead::where('code', 'E-3001')->first(); // updated to 'revenue' earlier
            
            for ($i = 0; $i < 10; $i++) {
                $daysAgo = rand(1, 30);
                $bookDate = now()->subDays($daysAgo);
                
                $totalAmount = rand(5, 20) * 1000;
                $commissionPercentage = 20; // 20% commission
                $commissionAmount = $totalAmount * ($commissionPercentage / 100);
                $vendorAmount = $totalAmount - $commissionAmount;

                $booking = Booking::create([
                    'booking_number' => 'FAKE-' . strtoupper(Str::random(8)),
                    'customer_id' => $customer->id,
                    'vendor_id' => $vendor->id,
                    'property_id' => $property->id,
                    'check_in' => $bookDate->copy()->addDays(2),
                    'check_out' => $bookDate->copy()->addDays(5),
                    'total_amount' => $totalAmount,
                    'final_amount' => $totalAmount,
                    'commission_percentage' => $commissionPercentage,
                    'commission_amount' => $commissionAmount,
                    'vendor_amount' => $vendorAmount,
                    'adults' => 2,
                    'children' => 0,
                    'guest_count' => 2,
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'created_at' => $bookDate,
                    'updated_at' => $bookDate
                ]);

                // Update Vendor Wallet
                $vendorWallet->balance += $vendorAmount;
                $vendorWallet->total_earned += $vendorAmount;
                $vendorWallet->save();

                // Generate Old Transactions for wallet history
                Transaction::create([
                    'vendor_id' => $vendor->id,
                    'amount' => $vendorAmount,
                    'type' => 'credit',
                    'category' => 'booking',
                    'description' => "Earned from Booking #{$booking->booking_number}",
                    'reference_id' => $booking->id,
                    'balance_after' => $vendorWallet->balance,
                    'created_at' => $bookDate,
                    'updated_at' => $bookDate
                ]);

                // Generate Journal Entry
                if ($cashHead && $vendorPayableHead && $commissionIncomeHead) {
                    $journal = JournalEntry::create([
                        'transaction_date' => $bookDate,
                        'reference_type' => Booking::class,
                        'reference_id' => $booking->id,
                        'description' => "Booking #{$booking->booking_number} generated revenue.",
                        'created_at' => $bookDate,
                        'updated_at' => $bookDate
                    ]);

                    // Debit Cash 
                    $journal->lines()->create(['account_head_id' => $cashHead->id, 'type' => 'debit', 'amount' => $booking->final_amount]);
                    
                    // Credit Vendor Payable
                    if ($booking->vendor_amount > 0) {
                        $journal->lines()->create(['account_head_id' => $vendorPayableHead->id, 'type' => 'credit', 'amount' => $booking->vendor_amount]);
                    }
                    // Credit Commission Income
                    if ($booking->commission_amount > 0) {
                        $journal->lines()->create(['account_head_id' => $commissionIncomeHead->id, 'type' => 'credit', 'amount' => $booking->commission_amount]);
                    }
                }
            }

            // Also seed a couple of random expenses to populate P&L further
            $taxExpenseHead = AccountHead::where('code', 'X-5001')->first();
            $serverExpenseHead = AccountHead::where('code', 'X-5002')->first();
            
            if ($taxExpenseHead && $cashHead) {
                $j1 = JournalEntry::create([
                    'transaction_date' => now()->subDays(5),
                    'description' => "GST Tax Paid to Govt",
                ]);
                $j1->lines()->create(['account_head_id' => $taxExpenseHead->id, 'type' => 'debit', 'amount' => 5000]);
                $j1->lines()->create(['account_head_id' => $cashHead->id, 'type' => 'credit', 'amount' => 5000]);
            }

            if ($serverExpenseHead && $cashHead) {
                $j2 = JournalEntry::create([
                    'transaction_date' => now()->subDays(10),
                    'description' => "Digital Ocean Server Invoice",
                ]);
                $j2->lines()->create(['account_head_id' => $serverExpenseHead->id, 'type' => 'debit', 'amount' => 1500]);
                $j2->lines()->create(['account_head_id' => $cashHead->id, 'type' => 'credit', 'amount' => 1500]);
            }
        });
    }
}
