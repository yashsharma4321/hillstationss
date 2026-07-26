<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class WishlistController extends Controller
{
    #[OA\Get(path: "/api/customer/wishlist", summary: "Get customer's wishlist", tags: ["Wishlist"], security: [['bearerAuth' => []]])]
    #[OA\Response(
        response: 200,
        description: "List of wishlisted properties",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function index()
    {
        $wishlists = Wishlist::where('user_id', auth()->id())
            ->with(['property' => function ($q) {
                $q->where('status', 'active')
                    ->with(['category', 'destination.state', 'roomTypes']);
            }])
            ->latest()
            ->get()
            ->filter(fn($w) => $w->property !== null); // Remove if property was deleted/deactivated

        $formatted = $wishlists->map(function ($wishlist) {
            $prop = $wishlist->property;

            $mainImage = null;
            $gallery = $prop->gallery ?? [];

            if (!empty($gallery)) {
                $first = reset($gallery);
                $path = is_array($first) ? ($first['image'] ?? null) : $first;
                $mainImage = $path ? url(Storage::url($path)) : null;
            }

            $galleryImages = collect($gallery)->map(function ($item) {
                $path = is_array($item) ? ($item['image'] ?? '') : $item;
                $alt = is_array($item) ? ($item['alt'] ?? '') : '';
                return [
                    'image' => $path ? url(Storage::url($path)) : null,
                    'alt' => $alt,
                ];
            })->filter(fn($i) => $i['image'] !== null)->values();

            return [
                'wishlist_id' => $wishlist->id,
                'added_at' => $wishlist->created_at->toDateTimeString(),
                'property' => [
                    'id' => $prop->id,
                    'name' => $prop->name,
                    'slug' => $prop->slug,
                    'city' => $prop->city,
                    'state' => $prop->destination->state->name ?? $prop->state,
                    'bedrooms' => $prop->total_bedrooms,
                    'bathrooms' => $prop->total_bathrooms,
                    'guests' => $prop->max_guests,
                    'rating' => $prop->average_rating ?? '4.90',
                    'price' => $prop->roomTypes->min('base_price') ?? '0.00',
                    'amount' => $prop->amount ?? 0,
                    'image' => $mainImage,
                    'images' => $galleryImages,
                    'category' => $prop->category->name ?? null,
                    'destination' => $prop->destination->name ?? null,
                ],
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $formatted
        ]);
    }

    #[OA\Post(path: "/api/customer/wishlist", summary: "Add property to wishlist (toggle)", tags: ["Wishlist"], security: [['bearerAuth' => []]])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["property_id"],
            properties: [
                new OA\Property(property: "property_id", type: "integer", example: 1)
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Wishlist toggled")]
    public function toggle(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $userId = auth()->id();
        $propertyId = $request->property_id;

        $existing = Wishlist::where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Property removed from wishlist.',
                'wishlisted' => false,
            ]);
        }

        Wishlist::create([
            'user_id' => $userId,
            'property_id' => $propertyId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Property added to wishlist.',
            'wishlisted' => true,
        ]);
    }

    #[OA\Delete(path: "/api/customer/wishlist/{id}", summary: "Remove property from wishlist", tags: ["Wishlist"], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: "id", in: "path", required: true, description: "Wishlist entry ID", schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Removed from wishlist")]
    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->findOrFail($id);

        $wishlist->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Property removed from wishlist.',
        ]);
    }

    #[OA\Get(path: "/api/customer/wishlist/check/{property_id}", summary: "Check if property is wishlisted", tags: ["Wishlist"], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: "property_id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Wishlist status")]
    public function check($propertyId)
    {
        $exists = Wishlist::where('user_id', auth()->id())
            ->where('property_id', $propertyId)
            ->exists();

        return response()->json([
            'status' => 'success',
            'wishlisted' => $exists,
        ]);
    }
}
