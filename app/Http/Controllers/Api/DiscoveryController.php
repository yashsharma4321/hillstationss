<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class DiscoveryController extends Controller
{
    #[OA\Get(path: "/api/discovery/best-rates", summary: "Get best rates villas grouped by state and destination", tags: ["Discovery"])]
    #[OA\Response(
        response: 200,
        description: "List of states with destinations and their top villas",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
   public function bestRates()
{
    $destinations = \App\Models\Destination::where('is_best_rate', 1)
        ->where('status', 'active')
        ->get();

    $result = $destinations->map(function ($destination) {

        $properties = Property::where('destination_id', $destination->id)
            ->where('status', 'active')
            ->with([
                'roomTypes' => function ($q) {
                    $q->select('property_id', 'base_price')->orderBy('base_price', 'asc');
                },
                'amenities' => function ($a) {
                    $a->select('amenities.id', 'name', 'icon');
                }
            ])
            ->latest()
            ->limit(8)
            ->get();

        $formattedProperties = $properties->map(function ($prop) {

            $minPrice = $prop->roomTypes->min('base_price') ?? 0;

            $mainImage = null;
            $gallery = $prop->gallery;

            if (!empty($gallery)) {
                $first = reset($gallery);
                $path = is_array($first) ? ($first['image'] ?? null) : $first;
                $mainImage = $path ? url(Storage::url($path)) : null;
            }

            $galleryImages = collect($prop->gallery ?? [])->map(function ($item) {
                $path = is_array($item) ? ($item['image'] ?? '') : $item;
                $alt = is_array($item) ? ($item['alt'] ?? '') : '';

                return [
                    'image' => $path ? url(Storage::url($path)) : null,
                    'alt' => $alt,
                ];
            })->filter(fn($i) => $i['image'] !== null)->values();

            return [
                'id' => $prop->id,
                'name' => $prop->name,
                'slug' => $prop->slug,
                'guests' => $prop->max_guests,
                'bedrooms' => $prop->total_bedrooms,
                'bathrooms' => $prop->total_bathrooms,
                'latitude' => $prop->latitude,
                'longitude' => $prop->longitude,
                'rating' => $prop->average_rating ?? '4.90',

                // 👇 Sirf ye line change ki hai
                'price' => $prop->amount,

                'amount' => $prop->amount ?? 0,
                'image' => $mainImage,
                'images' => $galleryImages,
                'amenities' => $prop->amenities->map(function ($amenity) {
                    return [
                        'id' => $amenity->id,
                        'name' => $amenity->name,
                        'icon' => $amenity->icon ? url(Storage::url($amenity->icon)) : null
                    ];
                })
            ];
        });

        return [
            'id' => $destination->id,
            'name' => $destination->name,
            'slug' => $destination->slug,
            'properties' => $formattedProperties
        ];
    })->values();

    return response()->json([
        'status' => 'success',
        'data' => $result
    ]);
}
    #[OA\Get(path: "/api/discovery/for-you", summary: "Get 'Properties for You' villas grouped by BHK", tags: ["Discovery"])]
    #[OA\Response(
        response: 200,
        description: "List of properties grouped by BHK count",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function propertiesForYou()
    {
        $properties = Property::where('is_featured', 1)
            ->where('status', 'active')
            ->with([
                'roomTypes' => function ($q) {
                    $q->select('property_id', 'base_price')->orderBy('base_price', 'asc');
                }
            ])
            ->get();

        $grouped = $properties->groupBy('total_bedrooms');

        $result = $grouped->map(function ($items, $bhk) {
            return [
                'title' => $bhk . ' BHK',
                'bhk_count' => (int) $bhk,
                'properties' => $items->map(function ($prop) {
                    $minPrice = $prop->roomTypes->min('base_price') ?? 0;

                    $mainImage = null;
                    $gallery = $prop->gallery;
                    if (!empty($gallery)) {
                        $first = reset($gallery);
                        $path = is_array($first) ? ($first['image'] ?? null) : $first;
                        $mainImage = $path ? url(Storage::url($path)) : null;
                    }

                    $galleryImages = collect($prop->gallery ?? [])->map(function ($item) {
                        $path = is_array($item) ? ($item['image'] ?? '') : $item;
                        $alt = is_array($item) ? ($item['alt'] ?? '') : '';
                        return [
                            'image' => $path ? url(Storage::url($path)) : null,
                            'alt' => $alt,
                        ];
                    })->filter(fn($i) => $i['image'] !== null)->values();

                    return [
                        'id' => $prop->id,
                        'name' => $prop->name,
                        'slug' => $prop->slug,
                        'guests' => $prop->max_guests,
                        'bedrooms' => $prop->total_bedrooms,
                        'bathrooms' => $prop->total_bathrooms,
                        'rating' => $prop->average_rating ?? '4.90',
                        'price' => $minPrice,
                        'amount' => $prop->amount ?? 0,
                        'image' => $mainImage,
                        'images' => $galleryImages
                    ];
                })
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }

    #[OA\Get(path: "/api/discovery/menu-destinations", summary: "Get destinations marked to be shown in menu grouped by state", tags: ["Discovery"])]
    #[OA\Response(
        response: 200,
        description: "List of states with menu destinations",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function menuDestinations()
    {
        $states = \App\Models\State::where('status', 'active')
            ->with(['destinations' => function($q) {
                $q->where('show_in_menu', 1)->where('status', 'active')->orderBy('name', 'asc');
            }])
            ->get()
            ->filter(function($state) {
                return $state->destinations->count() > 0;
            });

        $result = $states->map(function ($state) {
            $formattedDestinations = $state->destinations->map(function ($dest) {
                return [
                    'id' => $dest->id,
                    'name' => $dest->name,
                    'slug' => $dest->slug,
                    'latitude' => $dest->latitude,
                    'longitude' => $dest->longitude,
                    'image' => $dest->image ? url(Storage::url($dest->image)) : null,
                ];
            });

            return [
                'state_id' => $state->id,
                'state_name' => $state->name,
                'state_slug' => $state->slug,
                'destinations' => $formattedDestinations
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }

    #[OA\Get(path: "/api/discovery/destination-properties", summary: "Get properties for a specific destination marked for menu", tags: ["Discovery"])]
    #[OA\Parameter(name: "destination_slug", in: "query", required: true, description: "The slug of the destination", schema: new OA\Schema(type: "string"))]
    #[OA\Response(
        response: 200,
        description: "List of properties for the destination",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function destinationProperties(Request $request)
    {
        $request->validate([
            'destination_slug' => 'required|exists:destinations,slug'
        ]);

        $destination = Destination::where('slug', $request->destination_slug)
            ->select('id', 'name', 'slug')
            ->first();

        $properties = Property::where('destination_id', $destination->id)
            ->where('show_in_menu', 1)
            ->where('status', 'active')
            ->select('id', 'name', 'slug', 'total_bedrooms', 'total_bathrooms', 'max_guests', 'city', 'state', 'gallery', 'average_rating', 'amount')
            ->get()
            ->map(function ($prop) {
                $gallery = collect($prop->gallery ?? [])->map(function ($item) {
                    $path = is_array($item) ? ($item['image'] ?? '') : $item;
                    $alt = is_array($item) ? ($item['alt'] ?? '') : '';
                    return [
                        'image' => $path ? url(Storage::url($path)) : null,
                        'alt' => $alt,
                    ];
                })->filter(fn($i) => $i['image'] !== null)->values();

                return [
                    'id' => $prop->id,
                    'name' => $prop->name,
                    'slug' => $prop->slug,
                    'bedrooms' => $prop->total_bedrooms,
                    'bathrooms' => $prop->total_bathrooms,
                    'guests' => $prop->max_guests,
                    'city' => $prop->city,
                    'state' => $prop->state,
                    'rating' => $prop->average_rating ?? '4.90',
                    'amount' => $prop->amount ?? 0,
                    'image' => $gallery->first()['image'] ?? null,
                    'images' => $gallery,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'destination' => [
                    'id' => $destination->id,
                    'name' => $destination->name,
                    'slug' => $destination->slug,
                ],
                'properties' => $properties,
            ]
        ]);
    }

    #[OA\Get(path: "/api/discovery/menu-categories", summary: "Get categories marked to be shown in menu", tags: ["Discovery"])]
    #[OA\Response(
        response: 200,
        description: "List of menu categories",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function menuCategories()
    {
        $categories = PropertyCategory::where('show_in_menu', 1)
            ->select('id', 'name', 'slug', 'icon', 'category_group')
            ->orderBy('name', 'asc')
            ->get()
            ->groupBy('category_group')
            ->map(function ($items, $group) {
                return [
                    'group_name' => $group ?: 'OTHER',
                    'categories' => $items->map(function ($cat) {
                        return [
                            'id' => $cat->id,
                            'name' => $cat->name,
                            'slug' => $cat->slug,
                            'icon' => $cat->icon ? url(Storage::url($cat->icon)) : null
                        ];
                    })
                ];
            })->values();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    #[OA\Get(path: "/api/discovery/category-properties", summary: "Get properties for a specific category marked for menu", tags: ["Discovery"])]
    #[OA\Parameter(name: "category_slug", in: "query", required: true, description: "The slug of the category", schema: new OA\Schema(type: "string"))]
    #[OA\Response(
        response: 200,
        description: "List of properties for the category",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "object")
            ]
        )
    )]
    public function categoryProperties(Request $request)
    {
        $request->validate([
            'category_slug' => 'required|exists:property_categories,slug'
        ]);

        $category = PropertyCategory::where('slug', $request->category_slug)
            ->select('id', 'name', 'slug')
            ->first();

        $properties = Property::where('category_id', $category->id)
            ->where('show_in_menu', 1)
            ->where('status', 'active')
            ->select('id', 'name', 'slug', 'total_bedrooms', 'total_bathrooms', 'max_guests', 'city', 'state', 'gallery', 'average_rating', 'amount')
            ->get()
            ->map(function ($prop) {
                $gallery = collect($prop->gallery ?? [])->map(function ($item) {
                    $path = is_array($item) ? ($item['image'] ?? '') : $item;
                    $alt = is_array($item) ? ($item['alt'] ?? '') : '';
                    return [
                        'image' => $path ? url(Storage::url($path)) : null,
                        'alt' => $alt,
                    ];
                })->filter(fn($i) => $i['image'] !== null)->values();

                return [
                    'id' => $prop->id,
                    'name' => $prop->name,
                    'slug' => $prop->slug,
                    'bedrooms' => $prop->total_bedrooms,
                    'bathrooms' => $prop->total_bathrooms,
                    'guests' => $prop->max_guests,
                    'city' => $prop->city,
                    'state' => $prop->state,
                    'rating' => $prop->average_rating ?? '4.90',
                    'amount' => $prop->amount ?? 0,
                    'image' => $gallery->first()['image'] ?? null,
                    'images' => $gallery,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ],
                'properties' => $properties,
            ]
        ]);
    }
}
