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
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|string|max:20',
            'check_in'    => 'required|date|after_or_equal:today',
            'check_out'   => 'required|date|after:check_in',
            'adults'      => 'nullable|integer|min:1|max:50',
            'message'     => 'nullable|string|max:1000',
        ]);

        $property = Property::findOrFail($validated['property_id']);

        if (!$property->vendor_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This property has no vendor assigned.',
            ], 422);
        }

        $bookingRequest = BookingRequest::create([
            'property_id' => $property->id,
            'vendor_id'   => $property->vendor_id,
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'] ?? null,
            'check_in'    => $validated['check_in'],
            'check_out'   => $validated['check_out'],
            'adults'      => $validated['adults'] ?? 1,
            'message'     => $validated['message'] ?? null,
            'status'      => 'pending',
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
