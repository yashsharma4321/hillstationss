<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

use App\Models\Booking;
use App\Models\Property;
use App\Models\VendorWallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

#[OA\Tag(name: "Bookings", description: "Booking management endpoints")]
class BookingController extends Controller
{
    #[OA\Get(
        path: "/api/customer/bookings",
        summary: "List logged in customer's bookings",
        tags: ["Bookings"],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "List of bookings",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "object")
            ]
        )
    )]
    public function index()
    {
      
        $bookings = Booking::where('customer_id', auth()->id())
            ->with([
                'property' => function ($q) {
                    $q->select('id', 'name', 'slug', 'city', 'state','gallery');
                },
                'review'
            ])
            ->latest()
            ->paginate(10);

        $bookings->getCollection()->transform(function ($booking) {
            $booking->price = $booking->final_amount;
            if ($booking->property) {
                $booking->property->is_wishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
                    ->where('property_id', $booking->property_id)
                    ->exists();
            }
            return $booking;
        });

        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ]);
    }

    #[OA\Get(
        path: "/api/booking/{id}",
        summary: "Get booking details by ID",
        tags: ["Bookings"],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "Booking ID",
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Response(
        response: 200,
        description: "Booking details",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "object")
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Booking not found",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "error"),
                new OA\Property(property: "message", type: "string", example: "Booking not found")
            ]
        )
    )]
    public function show($id)
    {
        $booking = Booking::with([
            'property',
            'review',
            'vendor'
        ])->find($id);

        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $booking
        ]);
    }

    #[OA\Post(
        path: "/api/customer/bookings",
        summary: "Initiate a new booking",
        description: "Creates a pending booking without payment confirmation. Returns booking_id for payment confirmation.",
        tags: ["Bookings"],
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["property_id", "check_in", "check_out", "total_amount"],
            properties: [
                new OA\Property(property: "property_id", type: "integer", example: 1, description: "ID of the property to book"),
                new OA\Property(property: "check_in", type: "string", format: "date", example: "2026-06-01", description: "Check-in date"),
                new OA\Property(property: "check_out", type: "string", format: "date", example: "2026-06-05", description: "Check-out date"),
                new OA\Property(property: "total_amount", type: "number", format: "float", example: 5000, description: "Total booking amount"),
                new OA\Property(property: "subtotal", type: "number", format: "float", example: 4500, description: "Subtotal before tax"),
                new OA\Property(property: "discount", type: "number", format: "float", example: 500, description: "Discount amount"),
                new OA\Property(property: "gst", type: "number", format: "float", example: 500, description: "GST amount"),
                new OA\Property(property: "adults", type: "integer", example: 2, description: "Number of adults"),
                new OA\Property(property: "children", type: "integer", example: 1, description: "Number of children"),
                new OA\Property(property: "room_type_id", type: "integer", example: 5, description: "Room type ID (optional)"),
                new OA\Property(property: "coupon_code", type: "string", example: "SAVE10", description: "Coupon code (optional)"),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Booking initiated successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "message", type: "string", example: "Booking initiated. Please complete payment."),
                new OA\Property(property: "booking_id", type: "integer", example: 123),
                new OA\Property(property: "data", type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 123),
                        new OA\Property(property: "booking_number", type: "string", example: "BST-ABC123DEF"),
                        new OA\Property(property: "property", type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Luxury Villa"),
                                new OA\Property(property: "slug", type: "string", example: "luxury-villa")
                            ]
                        ),
                        new OA\Property(property: "check_in", type: "string", format: "date", example: "2026-06-01"),
                        new OA\Property(property: "check_out", type: "string", format: "date", example: "2026-06-05"),
                        new OA\Property(property: "total_amount", type: "number", example: 5000),
                        new OA\Property(property: "final_amount", type: "number", example: 5000),
                        new OA\Property(property: "status", type: "string", example: "pending"),
                        new OA\Property(property: "payment_status", type: "string", example: "pending"),
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Property not found",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "error"),
                new OA\Property(property: "message", type: "string", example: "Property not found.")
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Validation error or vendor not assigned",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "error"),
                new OA\Property(property: "message", type: "string", example: "This property has no vendor assigned.")
            ]
        )
    )]
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'adults' => 'nullable|integer|min:1',
            'children' => 'nullable|integer|min:0',
        ]);

        $property = Property::find($request->property_id);
        if (!$property) {
            return response()->json([
                'status' => 'error',
                'message' => 'Property not found.'
            ], 404);
        }

        $vendor = $property->vendor;
        if (!$vendor) {
            return response()->json([
                'status' => 'error',
                'message' => 'This property has no vendor assigned.'
            ], 422);
        }

        $coupon_id = null;
        if ($request->coupon_code) {
            $coupon = \App\Models\Coupon::where('code', $request->coupon_code)->first();
            if ($coupon) {
                $coupon_id = $coupon->id;
                $coupon->increment('used_count');
            }
        }

        $base_amount = $request->subtotal ?? $request->total_amount;
        $discount_amount = $request->discount ?? 0;
        $gst_amount = $request->gst ?? 0;
        $final_amount = $request->total_amount; 

        $commission_percentage = $vendor->commission_rate ?? 10;
        $commission_amount = $base_amount * ($commission_percentage / 100);
        $vendor_amount = $base_amount - $commission_amount;

        // Create booking with pending payment status (NO accounting/wallet yet)
        $booking = DB::transaction(function () use (
            $request, $property, $vendor, $coupon_id,
            $base_amount, $discount_amount, $gst_amount, $final_amount,
            $commission_percentage, $commission_amount, $vendor_amount
        ) {
            $booking = Booking::create([
                'booking_number' => 'BST-' . strtoupper(Str::random(10)),
                'customer_id' => auth()->id(),
                'vendor_id' => $vendor->id,
                'property_id' => $request->property_id,
                'room_id' => $request->room_type_id ?? null,
                'coupon_id' => $coupon_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'total_amount' => $base_amount,
                'discount_amount' => $discount_amount,
                'gst_amount' => $gst_amount,
                'final_amount' => $final_amount,
                'commission_percentage' => $commission_percentage,
                'commission_amount' => $commission_amount,
                'vendor_amount' => $vendor_amount,
                'adults' => $request->adults ?? 1,
                'children' => $request->children ?? 0,
                'guest_count' => ($request->adults ?? 1) + ($request->children ?? 0),
                'status' => 'pending',
                'payment_status' => 'pending',
                // NO Razorpay fields here yet - payment not completed
            ]);

            // Property Booking calendar entry (temporary hold)
            if ($request->property_id && $request->check_in && $request->check_out) {
                \App\Models\PropertyBooking::create([
                    'property_id' => $request->property_id,
                    'booking_id' => $booking->id,
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'status' => 'pending', // Changed to pending until payment
                ]);
            }

            return $booking;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Booking initiated. Please complete payment.',
            'data' => $booking->load('property:id,name,slug'),
            'booking_id' => $booking->id,
        ]);
    }

    #[OA\Post(
        path: "/api/customer/booking-final",
        summary: "Confirm booking payment",
        description: "Confirm payment for a pending booking. This will update payment status, credit vendor wallet, and create accounting entries.",
        tags: ["Bookings"],
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["booking_id", "razorpay_payment_id", "razorpay_order_id", "razorpay_signature"],
            properties: [
                new OA\Property(property: "booking_id", type: "integer", example: 123, description: "ID of the pending booking"),
                new OA\Property(property: "razorpay_payment_id", type: "string", example: "pay_29QQoUBi66xm2f", description: "Razorpay payment ID"),
                new OA\Property(property: "razorpay_order_id", type: "string", example: "order_9A33XWu170gUtm", description: "Razorpay order ID"),
                new OA\Property(property: "razorpay_signature", type: "string", example: "9efc8b9f5e8b3a6d1c4e2f8a7b9d5e3f1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c6d", description: "Razorpay signature for verification"),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Payment confirmed successfully",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "message", type: "string", example: "Payment confirmed and booking completed successfully!"),
                new OA\Property(property: "data", type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 123),
                        new OA\Property(property: "booking_number", type: "string", example: "BST-ABC123DEF"),
                        new OA\Property(property: "status", type: "string", example: "confirmed"),
                        new OA\Property(property: "payment_status", type: "string", example: "paid"),
                        new OA\Property(property: "property", type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Luxury Villa")
                            ]
                        )
                    ]
                ),
                new OA\Property(property: "wallet", type: "object",
                    properties: [
                        new OA\Property(property: "vendor_amount_credited", type: "number", example: 4500),
                        new OA\Property(property: "commission_deducted", type: "number", example: 500),
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Validation error or payment already processed",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "error"),
                new OA\Property(property: "message", type: "string", example: "Payment already processed for this booking.")
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Booking not found",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "error"),
                new OA\Property(property: "message", type: "string", example: "No query results for model [App\\Models\\Booking]")
            ]
        )
    )]
    public function bookingfinal(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);
        
        // Find the existing booking
        $booking = Booking::findOrFail($request->booking_id);
        
        // Verify payment signature here (Razorpay verification)
        // Add your Razorpay verification logic
        
        // Check if payment is already processed
        if ($booking->payment_status === 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment already processed for this booking.'
            ], 422);
        }
        
        $vendor = $booking->vendor;
        
        DB::transaction(function () use ($request, $booking, $vendor) {
            // Update booking with payment info
            $booking->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature,
                'payment_status' => 'paid',
                'status' => 'confirmed', // or whatever status you use
            ]);
            
            // Update PropertyBooking status to confirmed
            \App\Models\PropertyBooking::where('booking_id', $booking->id)
                ->update(['status' => 'confirmed']);
            
            // ── Credit Vendor Wallet (only after successful payment) ──
            if ($booking->vendor_amount > 0) {
                $wallet = VendorWallet::firstOrCreate(
                    ['vendor_id' => $vendor->id],
                    ['balance' => 0, 'total_earned' => 0]
                );
                
                $wallet->increment('balance', $booking->vendor_amount);
                $wallet->increment('total_earned', $booking->vendor_amount);
                $wallet->refresh();
                
                Transaction::create([
                    'vendor_id'      => $vendor->id,
                    'amount'         => $booking->vendor_amount,
                    'type'           => 'credit',
                    'category'       => 'booking_earning',
                    'description'    => "Earning from booking #{$booking->booking_number}",
                    'reference_id'   => $booking->id,
                    'balance_after'  => $wallet->balance,
                ]);
                
                \Illuminate\Support\Facades\Log::info("Wallet credited for vendor #{$vendor->id}: +₹{$booking->vendor_amount}, new balance: ₹{$wallet->balance}");
            }
            
            // ── Accounting / Journal Entry (only after successful payment) ──
            $cashHead = \App\Models\AccountHead::where('code', 'A-1002')->first();
            $vendorPayableHead = \App\Models\AccountHead::where('code', 'L-2001')->first();
            $commissionIncomeHead = \App\Models\AccountHead::where('code', 'E-3001')->first();
            
            if ($cashHead && $vendorPayableHead && $commissionIncomeHead) {
                $journal = \App\Models\JournalEntry::create([
                    'transaction_date' => now(),
                    'reference_type' => Booking::class,
                    'reference_id' => $booking->id,
                    'description' => "Booking #{$booking->booking_number} - Payment confirmed",
                ]);
                
                // Debit Cash/Bank
                $journal->lines()->create([
                    'account_head_id' => $cashHead->id, 
                    'type' => 'debit', 
                    'amount' => $booking->final_amount
                ]);
                
                // Credit Vendor Payable (Liability)
                if ($booking->vendor_amount > 0) {
                    $journal->lines()->create([
                        'account_head_id' => $vendorPayableHead->id, 
                        'type' => 'credit', 
                        'amount' => $booking->vendor_amount
                    ]);
                }
                
                // Credit Commission Income
                if ($booking->commission_amount > 0) {
                    $journal->lines()->create([
                        'account_head_id' => $commissionIncomeHead->id, 
                        'type' => 'credit', 
                        'amount' => $booking->commission_amount
                    ]);
                }
            }
        });
        
        return response()->json([
            'status' => 'success',
            'message' => 'Payment confirmed and booking completed successfully!',
            'data' => $booking->fresh()->load('property'),
            'wallet' => [
                'vendor_amount_credited' => $booking->vendor_amount,
                'commission_deducted' => $booking->commission_amount,
            ]
        ]);
    }
}