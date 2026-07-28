<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

use App\Models\Booking;
use App\Models\CancellationRule;
use App\Models\CancellationBooking;
use App\Models\VendorWallet;
use App\Models\Transaction;
use App\Models\AccountHead;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[OA\Tag(name: "Cancellations", description: "Booking cancellation endpoints")]
class CancellationController extends Controller
{
    #[OA\Post(
        path: "/api/customer/booking/cancel",
        summary: "Cancel a booking",
        description: "Cancel a confirmed booking and process refund based on cancellation rules",
        tags: ["Cancellations"],
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["booking_id"],
            properties: [
                new OA\Property(property: "booking_id", type: "integer", example: 123),
                new OA\Property(property: "reason", type: "string", example: "Change of plans")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Booking cancelled successfully"
    )]
    #[OA\Response(
        response: 422,
        description: "Validation error or cannot cancel"
    )]
    #[OA\Response(
        response: 404,
        description: "Cancellation rule not found"
    )]
    public function cancelBooking(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'reason' => 'nullable|string|max:500'
        ]);

        $booking = Booking::with(['property', 'vendor'])->findOrFail($request->booking_id);

        // Check if booking can be cancelled
        if ($booking->status === 'cancelled') {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking is already cancelled.'
            ], 422);
        }

        if ($booking->payment_status !== 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot cancel unpaid booking.'
            ], 422);
        }

        // Calculate days before check-in
        $checkinDate = Carbon::parse($booking->check_in);
        $currentDate = now();
        $daysBeforeCheckin = $checkinDate->diffInDays($currentDate);

        // Get applicable cancellation rule
        $rule = CancellationRule::where('days_before', '<=', $daysBeforeCheckin)
            ->orderBy('days_before', 'desc')
            ->first();

        if (!$rule) {
            return response()->json([
                'status' => 'error',
                'message' => 'No cancellation rule found for this booking.'
            ], 404);
        }

        // Calculate amounts
        $deductionPercentage = $rule->deduction_percentage;
        $deductionAmount = ($booking->final_amount * $deductionPercentage) / 100;
        $refundAmount = $booking->final_amount - $deductionAmount;

        // FIX: Pass $daysBeforeCheckin to the closure
        DB::transaction(function () use ($booking, $rule, $deductionPercentage, $deductionAmount, $refundAmount, $request, $daysBeforeCheckin) {
            
            // 1. Create cancellation record
            $cancellation = CancellationBooking::create([
                'booking_id' => $booking->id,
                'cancellation_rule_id' => $rule->id,
                'amount' => $booking->final_amount,
                'deduction_percentage' => $deductionPercentage,
                'deduction_amount' => $deductionAmount,
                'refund_amount' => $refundAmount,
                'reason' => $request->reason,
                'cancelled_at' => now()
            ]);

            // 2. Update booking status
            $booking->status = 'cancelled';
            $booking->cancellation_reason = $request->reason;
            $booking->cancellation_date = now();
            $booking->deduction_percentage = $deductionPercentage;
            $booking->deduction_amount = $deductionAmount;
            $booking->refund_amount = $refundAmount;
            $booking->save();

            // 3. Update PropertyBooking status
            \App\Models\PropertyBooking::where('booking_id', $booking->id)
                ->update(['status' => 'cancelled']);

            // 4. Reverse Vendor Wallet (deduct vendor earnings)
            if ($booking->vendor_amount > 0) {
                $wallet = VendorWallet::where('vendor_id', $booking->vendor_id)->first();
                
                if ($wallet) {
                    // Deduct vendor amount from wallet
                    $wallet->decrement('balance', $booking->vendor_amount);
                    $wallet->refresh();

                    // Create transaction record for deduction
                    Transaction::create([
                        'vendor_id' => $booking->vendor_id,
                        'amount' => $booking->vendor_amount,
                        'type' => 'debit',
                        'category' => 'cancellation_deduction',
                        'description' => "Cancellation deduction for booking #{$booking->booking_number}",
                        'reference_id' => $booking->id,
                        'balance_after' => $wallet->balance,
                    ]);
                }
            }

            // 5. Reverse Accounting Entries
            $this->reverseAccountingEntries($booking);

            // 6. Process refund (if any)
            if ($refundAmount > 0) {
                // Add your refund logic here (Razorpay refund)
                // $this->processRefund($booking, $refundAmount);
                
                // Create refund transaction record
                Transaction::create([
                    'vendor_id' => null,
                    'amount' => $refundAmount,
                    'type' => 'debit',
                    'category' => 'customer_refund',
                    'description' => "Refund for cancelled booking #{$booking->booking_number}",
                    'reference_id' => $booking->id,
                    'balance_after' => 0,
                ]);
            }

            // 7. Log cancellation
            \Illuminate\Support\Facades\Log::info("Booking #{$booking->booking_number} cancelled", [
                'booking_id' => $booking->id,
                'deduction_percentage' => $deductionPercentage,
                'deduction_amount' => $deductionAmount,
                'refund_amount' => $refundAmount,
                'days_before_checkin' => $daysBeforeCheckin
            ]);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Booking cancelled successfully.',
            'data' => [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'cancellation' => [
                    'deduction_percentage' => $deductionPercentage,
                    'deduction_amount' => $deductionAmount,
                    'refund_amount' => $refundAmount,
                    'refund_status' => $refundAmount > 0 ? 'Refund Initiated' : 'No Refund',
                    'reason' => $request->reason,
                    'cancelled_at' => now()
                ]
            ]
        ]);
    }

    /**
     * Reverse accounting entries for cancelled booking
     */
    private function reverseAccountingEntries($booking)
    {
        try {
            // Find the journal entry for this booking
            $journal = JournalEntry::where('reference_type', Booking::class)
                ->where('reference_id', $booking->id)
                ->first();

            if ($journal) {
                // Create reversal journal entry
                $reversalJournal = JournalEntry::create([
                    'transaction_date' => now(),
                    'reference_type' => Booking::class,
                    'reference_id' => $booking->id,
                    'description' => "Reversal: Booking #{$booking->booking_number} - Cancelled",
                ]);

                // Reverse all lines
                foreach ($journal->lines as $line) {
                    $reversalJournal->lines()->create([
                        'account_head_id' => $line->account_head_id,
                        'type' => $line->type === 'debit' ? 'credit' : 'debit',
                        'amount' => $line->amount,
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to reverse accounting entries: ' . $e->getMessage());
        }
    }

    /**
     * Process refund via Razorpay
     */
    private function processRefund($booking, $refundAmount)
    {
        // Add your Razorpay refund logic here
        // Example:
        // $razorpay = new RazorpayApiClient();
        // $refund = $razorpay->payment->refund($booking->razorpay_payment_id, [
        //     'amount' => $refundAmount * 100, // Convert to paise
        //     'speed' => 'normal',
        //     'receipt' => 'refund_' . $booking->id
        // ]);
        
        // Update booking with refund details
        $booking->refund_id = 'refund_' . $booking->id; // Store refund ID
        $booking->refund_status = 'initiated';
        $booking->save();
        
        return true;
    }

    #[OA\Get(
        path: "/api/customer/cancellations",
        summary: "Get customer's cancelled bookings",
        tags: ["Cancellations"],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "List of cancellations"
    )]
    public function getCancellations()
    {
        $cancellations = CancellationBooking::with(['booking.property'])
            ->whereHas('booking', function($q) {
                $q->where('customer_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $cancellations
        ]);
    }

    #[OA\Get(
        path: "/api/cancellation-rules",
        summary: "Get cancellation rules",
        tags: ["Cancellations"]
    )]
    #[OA\Response(
        response: 200,
        description: "List of cancellation rules"
    )]
    public function getRules()
    {
        $rules = CancellationRule::orderBy('days_before', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $rules
        ]);
    }

    #[OA\Get(
        path: "/api/cancellation-rules/calculate",
        summary: "Calculate cancellation charges",
        tags: ["Cancellations"]
    )]
    #[OA\Parameter(
        name: "booking_id",
        in: "query",
        required: true,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Calculated cancellation charges"
    )]
    #[OA\Response(
        response: 404,
        description: "Cancellation rule not found"
    )]
    public function calculateCancellationCharges(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id'
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        
        $checkinDate = Carbon::parse($booking->check_in);
        $currentDate = now();
        $daysBeforeCheckin = $checkinDate->diffInDays($currentDate);

        $rule = CancellationRule::where('days_before', '<=', $daysBeforeCheckin)
            ->orderBy('days_before', 'desc')
            ->first();

        if (!$rule) {
            return response()->json([
                'status' => 'error',
                'message' => 'No cancellation rule found'
            ], 404);
        }

        $deductionAmount = ($booking->final_amount * $rule->deduction_percentage) / 100;
        $refundAmount = $booking->final_amount - $deductionAmount;

        return response()->json([
            'status' => 'success',
            'data' => [
                'booking_id' => $booking->id,
                'booking_amount' => $booking->final_amount,
                'days_before_checkin' => $daysBeforeCheckin,
                'applicable_rule' => $rule,
                'deduction_percentage' => $rule->deduction_percentage,
                'deduction_amount' => $deductionAmount,
                'refund_amount' => $refundAmount
            ]
        ]);
    }
}