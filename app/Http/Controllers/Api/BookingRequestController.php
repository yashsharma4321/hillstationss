<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use App\Models\Property;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Booking Requests", description: "Enquiry / booking request from property detail page")]
class BookingRequestController extends Controller
{
    #[OA\Post(
        path: "/api/booking-request",
        summary: "Submit a booking enquiry for a property",
        tags: ["Booking Requests"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["property_id", "name", "email", "check_in", "check_out"],
            properties: [
                new OA\Property(property: "property_id",  type: "integer", example: 1),
                new OA\Property(property: "name",         type: "string",  example: "Rahul Sharma"),
                new OA\Property(property: "email",        type: "string",  example: "rahul@example.com"),
                new OA\Property(property: "phone",        type: "string",  example: "+919876543210"),
                new OA\Property(property: "check_in",     type: "string",  format: "date", example: "2026-09-01"),
                new OA\Property(property: "check_out",    type: "string",  format: "date", example: "2026-09-05"),
                new OA\Property(property: "adults",       type: "integer", example: 2),
                new OA\Property(property: "message",      type: "string",  example: "We are a family of 4."),
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Booking request submitted")]
    #[OA\Response(response: 422, description: "Validation error")]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'check_in'    => 'required|date|after_or_equal:today',
            'check_out'   => 'required|date|after:check_in',
            'total_amount'=> 'required|numeric|min:0',
            'subtotal'    => 'required|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0',
            'gst'         => 'nullable|numeric|min:0',
            'adults'      => 'required|integer|min:1|max:50',
            'children'    => 'nullable|integer|min:0|max:50',
            'room_type_id'=> 'nullable|exists:room_types,id',
            'coupon_code' => 'nullable|string|max:100',
            'message'     => 'nullable|string|max:1000',
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $property = Property::findOrFail($validated['property_id']);

        if (!$property->vendor_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This property has no vendor assigned.',
            ], 422);
        }

        $bookingRequest = BookingRequest::create([
            'property_id'  => $property->id,
            'vendor_id'    => $property->vendor_id,
            'user_id'      => $user->id,
            'name'         => $user->name,
            'email'        => $user->email,
            'phone'        => $user->phone,
            'check_in'     => $validated['check_in'],
            'check_out'    => $validated['check_out'],
            'total_amount' => $validated['total_amount'],
            'subtotal'     => $validated['subtotal'],
            'discount'     => $validated['discount'] ?? 0,
            'gst'          => $validated['gst'] ?? 0,
            'adults'       => $validated['adults'],
            'children'     => $validated['children'] ?? 0,
            'room_type_id' => $validated['room_type_id'] ?? null,
            'coupon_code'  => $validated['coupon_code'] ?? null,
            'message'      => $validated['message'] ?? null,
            'status'       => 'pending',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Booking request submitted successfully. We will contact you soon.',
            'data'    => [
                'id'         => $bookingRequest->id,
                'property'   => $property->name,
                'check_in'   => $bookingRequest->check_in->format('Y-m-d'),
                'check_out'  => $bookingRequest->check_out->format('Y-m-d'),
                'status'     => $bookingRequest->status,
            ],
        ], 201);
    }
}
