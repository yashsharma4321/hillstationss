<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReviewController extends Controller
{
    #[OA\Get(path: "/api/properties/{slug}/reviews", summary: "Get reviews for a property", tags: ["Reviews"])]
    #[OA\Parameter(name: "slug", in: "path", required: true, schema: new OA\Schema(type: "string"))]
    #[OA\Response(
        response: 200,
        description: "List of reviews for the property",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "object")
            ]
        )
    )]
    public function index($slug)
    {
        $property = Property::where('slug', $slug)->where('status', 'active')->first();

        if (!$property) {
            return response()->json([
                'status' => 'error',
                'message' => 'Property not found'
            ], 404);
        }

        $allReviews = Review::where('property_id', $property->id)->where('status', 'active')->get();
        
        $ratingDistribution = [
            5 => $allReviews->where('rating', 5)->count(),
            4 => $allReviews->where('rating', 4)->count(),
            3 => $allReviews->where('rating', 3)->count(),
            2 => $allReviews->where('rating', 2)->count(),
            1 => $allReviews->where('rating', 1)->count(),
        ];

        $categories = [
            'Amenities'     => number_format($allReviews->avg('amenities_rating') ?? 0, 1),
            'Cleanliness'   => number_format($allReviews->avg('cleanliness_rating') ?? 0, 1),
            'Communication' => number_format($allReviews->avg('communication_rating') ?? 0, 1),
            'Location'      => number_format($allReviews->avg('location_rating') ?? 0, 1),
            'Value'         => number_format($allReviews->avg('value_rating') ?? 0, 1),
        ];

        $reviews = Review::where('property_id', $property->id)
            ->where('status', 'active')
            ->with(['customer:id,name,avatar'])
            ->latest()
            ->paginate(10);

        $reviews->getCollection()->transform(function ($review) {
            return [
                'id'             => $review->id,
                'rating'         => number_format($review->rating, 1),
                'ratings'        => [
                    'amenities'     => $review->amenities_rating,
                    'cleanliness'   => $review->cleanliness_rating,
                    'communication' => $review->communication_rating,
                    'location'      => $review->location_rating,
                    'value'         => $review->value_rating,
                ],
                'comment'        => $review->comment,
                'vendor_response' => $review->vendor_response,
                'customer'       => [
                    'name'   => $review->customer->name ?? 'Guest',
                    'avatar' => $review->customer->avatar ? url(\Illuminate\Support\Facades\Storage::url($review->customer->avatar)) : null,
                ],
                'date'       => $review->created_at->format('d M Y'),
                'created_at' => $review->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'overall_rating' => number_format($property->average_rating ?? 0, 1),
                'total_reviews' => $allReviews->count(),
                'rating_distribution' => $ratingDistribution,
                'categories' => $categories,
                'reviews' => $reviews->items(),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ]
            ]
        ]);
    }

    #[OA\Post(path: "/api/customer/reviews", summary: "Submit a review for a booking", tags: ["Reviews"], security: [['bearerAuth' => []]])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["booking_id", "ratings", "comment"],
            properties: [
                new OA\Property(property: "booking_id", type: "string", example: "BK-0982"),
                new OA\Property(property: "comment", type: "string", example: "Amazing stay! Loved the view."),
                new OA\Property(
                    property: "ratings",
                    type: "object",
                    properties: [
                        new OA\Property(property: "amenities", type: "integer", example: 5),
                        new OA\Property(property: "cleanliness", type: "integer", example: 4),
                        new OA\Property(property: "communication", type: "integer", example: 5),
                        new OA\Property(property: "location", type: "integer", example: 4),
                        new OA\Property(property: "value", type: "integer", example: 5),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Review submitted")]
    #[OA\Response(response: 422, description: "Validation error")]
    public function store(Request $request)
    {
        $request->validate([
            'booking_id'              => 'required',
            'comment'                 => 'nullable|string|max:1000',
            'ratings'                 => 'required|array',
            'ratings.amenities'       => 'required|integer|min:1|max:5',
            'ratings.cleanliness'     => 'required|integer|min:1|max:5',
            'ratings.communication'   => 'required|integer|min:1|max:5',
            'ratings.location'        => 'required|integer|min:1|max:5',
            'ratings.value'           => 'required|integer|min:1|max:5',
        ]);

        // Find booking by booking_number OR id
        $booking = Booking::where('customer_id', auth()->id())
            ->where(function ($q) use ($request) {
                $q->where('id', $request->booking_id)
                  ->orWhere('booking_number', $request->booking_id);
            })
            ->first();

        if (!$booking) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Booking not found or does not belong to you.'
            ], 404);
        }

        // Check if already reviewed
        $existingReview = Review::where('booking_id', $booking->id)
            ->where('customer_id', auth()->id())
            ->first();

        if ($existingReview) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You have already reviewed this booking.'
            ], 422);
        }

        $ratings = $request->ratings;

        // Calculate average from the 5 category ratings
        $averageRating = round(
            array_sum([$ratings['amenities'], $ratings['cleanliness'], $ratings['communication'], $ratings['location'], $ratings['value']]) / 5,
            1
        );

        $review = Review::create([
            'booking_id'             => $booking->id,
            'property_id'            => $booking->property_id,
            'customer_id'            => auth()->id(),
            'rating'                 => $averageRating,
            'amenities_rating'       => $ratings['amenities'],
            'cleanliness_rating'     => $ratings['cleanliness'],
            'communication_rating'   => $ratings['communication'],
            'location_rating'        => $ratings['location'],
            'value_rating'           => $ratings['value'],
            'comment'                => $request->comment,
            'status'                 => 'active',
        ]);

        // Update property average rating
        $this->updatePropertyRating($booking->property_id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Review submitted successfully!',
            'data'    => [
                'id'             => $review->id,
                'booking_id'     => $booking->booking_number,
                'property_name'  => $booking->property->name ?? null,
                'ratings'        => [
                    'amenities'     => $review->amenities_rating,
                    'cleanliness'   => $review->cleanliness_rating,
                    'communication' => $review->communication_rating,
                    'location'      => $review->location_rating,
                    'value'         => $review->value_rating,
                ],
                'comment'        => $review->comment,
                'average_rating' => number_format($review->rating, 1),
            ]
        ], 201);
    }

    #[OA\Put(path: "/api/customer/reviews/{id}", summary: "Update your review", tags: ["Reviews"], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "rating", type: "integer", example: 4),
                new OA\Property(property: "comment", type: "string", example: "Updated review"),
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Review updated")]
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::where('id', $id)
            ->where('customer_id', auth()->id())
            ->first();

        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'Review not found.'
            ], 404);
        }

        $review->update($request->only(['rating', 'comment']));

        // Update property average rating
        $this->updatePropertyRating($review->property_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Review updated successfully!',
            'data' => $review->fresh()
        ]);
    }

    #[OA\Delete(path: "/api/customer/reviews/{id}", summary: "Delete your review", tags: ["Reviews"], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Review deleted")]
    public function destroy($id)
    {
        $review = Review::where('id', $id)
            ->where('customer_id', auth()->id())
            ->first();

        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'Review not found.'
            ], 404);
        }

        $propertyId = $review->property_id;
        $review->delete();

        // Update property average rating
        $this->updatePropertyRating($propertyId);

        return response()->json([
            'status' => 'success',
            'message' => 'Review deleted successfully.'
        ]);
    }

    /**
     * Recalculate and update the property's average_rating.
     */
    private function updatePropertyRating(int $propertyId): void
    {
        $avg = Review::where('property_id', $propertyId)
            ->where('status', 'active')
            ->avg('rating');

        Property::where('id', $propertyId)->update([
            'average_rating' => round($avg ?? 0, 2)
        ]);
    }
}
