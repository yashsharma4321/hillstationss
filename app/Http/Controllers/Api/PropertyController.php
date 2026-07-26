<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class PropertyController extends Controller
{
    #[OA\Get(path: "/api/properties", summary: "List all active properties with filters", tags: ["Properties"])]
    #[OA\Parameter(name: "destination", in: "query", description: "Destination slug", schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "state", in: "query", description: "State slug", schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "collection", in: "query", description: "Collection slug", schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "category", in: "query", description: "Category slug", schema: new OA\Schema(type: "string"))]
    #[OA\Parameter(name: "check_in", in: "query", description: "Format: YYYY-MM-DD", schema: new OA\Schema(type: "string", format: "date"))]
    #[OA\Parameter(name: "check_out", in: "query", description: "Format: YYYY-MM-DD", schema: new OA\Schema(type: "string", format: "date"))]
    #[OA\Parameter(name: "guests", in: "query", schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "rooms_requested", in: "query", schema: new OA\Schema(type: "integer"))]
    #[OA\Parameter(name: "lat", in: "query", schema: new OA\Schema(type: "number", format: "float"))]
    #[OA\Parameter(name: "lng", in: "query", schema: new OA\Schema(type: "number", format: "float"))]
    #[OA\Parameter(name: "radius", in: "query", description: "Radius in km (default 50)", schema: new OA\Schema(type: "number", format: "float"))]
    #[OA\Parameter(name: "bedrooms", in: "query", schema: new OA\Schema(type: "integer"))]
    #[OA\Response(
        response: 200,
        description: "Paginated list of properties with full details",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "object")
            ]
        )
    )]
    public function index(Request $request)
    {
        $query = Property::where('status', 'active')
            ->with(['category', 'destination.state', 'roomTypes', 'collections']);

        // Filter by Destination Slug
        if ($request->filled('destination')) {
            $query->whereHas('destination', function ($q) use ($request) {
                $q->where('slug', $request->destination);
            });
        }

        // Filter by State Slug
        if ($request->filled('state')) {
            $query->whereHas('destination.state', function ($q) use ($request) {
                $q->where('slug', $request->state);
            });
        }

        // Filter by Collection Slug
        if ($request->filled('collection')) {
            $query->whereHas('collections', function ($q) use ($request) {
                $q->where('slug', $request->collection);
            });
        }

        // Filter by Category Slug
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by Guests
        if ($request->filled('guests')) {
            $query->where('max_guests', '>=', $request->guests);
        }

        // Filter by Rooms Requested
        if ($request->filled('rooms_requested')) {
            $query->where('total_bedrooms', '>=', $request->rooms_requested);
        }

        // Filter by Bedrooms/BHK
        if ($request->filled('bedrooms')) {
            $query->where('total_bedrooms', $request->bedrooms);
        }

        // Geo-radius Search (Haversine Formula)
        if ($request->filled('lat') && $request->filled('lng')) {
            $lat = $request->lat;
            $lng = $request->lng;
            $radius = $request->input('radius', 50); // Default 50km

            $query->selectRaw("*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance", [$lat, $lng, $lat])
                ->having("distance", "<", $radius)
                ->orderBy("distance");
        }

        // Date-based Availability Filter
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $checkIn = $request->check_in;
            $checkOut = $request->check_out;

            // Exclude properties that have bookings overlapping the requested dates
            $query->whereDoesntHave('roomTypes.bookings', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', ['confirmed', 'paid'])
                    ->where(function ($sub) use ($checkIn, $checkOut) {
                        $sub->whereBetween('check_in', [$checkIn, $checkOut])
                            ->orWhereBetween('check_out', [$checkIn, $checkOut])
                            ->orWhere(function ($s) use ($checkIn, $checkOut) {
                                $s->where('check_in', '<=', $checkIn)
                                    ->where('check_out', '>=', $checkOut);
                            });
                    });
            });
        }

        $properties = $query->latest()->paginate(12);

        $properties->getCollection()->transform(function ($prop) {
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
                'id' => $prop->id,
                'name' => $prop->name,
                'slug' => $prop->slug,
                'city' => $prop->city,
                'state' => $prop->destination->state->name ?? $prop->state,
                'bedrooms' => $prop->total_bedrooms,
                'bathrooms' => $prop->total_bathrooms,
                'guests' => $prop->max_guests,
                'latitude' => $prop->latitude,
                'longitude' => $prop->longitude,
                'distance' => isset($prop->distance) ? round($prop->distance, 2) . ' km' : null,
                'rating' => $prop->average_rating ?? '4.90',
                'price' => $prop->amount ?? '0.00',
                'amount' => $prop->amount ?? 0,
                'image' => $mainImage,
                'images' => $galleryImages,
                'category' => $prop->category->name ?? null,
                'category_slug' => $prop->category->slug ?? null,
                'destination' => $prop->destination->name ?? null,
                'destination_slug' => $prop->destination->slug ?? null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'properties' => $properties->items(),
                'pagination' => [
                    'current_page' => $properties->currentPage(),
                    'last_page' => $properties->lastPage(),
                    'per_page' => $properties->perPage(),
                    'total' => $properties->total(),
                ]
            ]
        ]);
    }

    #[OA\Get(path: "/api/properties/{slug}", summary: "Get single property details by slug", tags: ["Properties"])]
    #[OA\Parameter(name: "slug", in: "path", required: true, description: "The slug of the property", schema: new OA\Schema(type: "string"))]
    #[OA\Response(
        response: 200,
        description: "Detailed property information",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "object")
            ]
        )
    )]
    public function show($slug)
    {
        $property = Property::where('slug', $slug)
            ->where('status', 'active')
            ->with([
                'category',
                'destination',
                'amenities',
                'rooms',
                'vendor.user',
                'cancellationRules'
            ])
            ->first();

        if (!$property) {
            return response()->json([
                'status' => 'error',
                'message' => 'Property not found'
            ], 404);
        }

        // Format Gallery
        $formattedGallery = collect($property->gallery)->map(function ($item) {
            $path = is_array($item) ? ($item['image'] ?? '') : $item;
            $alt = is_array($item) ? ($item['alt'] ?? '') : '';
            return [
                'image' => $path ? url(Storage::url($path)) : null,
                'alt' => $alt
            ];
        })->filter(fn($item) => $item['image'] !== null)->values();

        // Format Amenities
        $formattedAmenities = $property->amenities->map(function ($amenity) {
            return [
                'id' => $amenity->id,
                'name' => $amenity->name,
                'icon' => $amenity->icon ? url(Storage::url($amenity->icon)) : null
            ];
        });

        // Fetch Booked Dates
        $bookedDates = [];
        $propertyBookings = \App\Models\PropertyBooking::where('property_id', $property->id)
            ->whereIn('status', ['confirmed', 'booked'])
            
            ->get();

        foreach ($propertyBookings as $pb) {
            $period = \Carbon\CarbonPeriod::create($pb->check_in, $pb->check_out);
            foreach ($period as $date) {
                $bookedDates[] = $date->format('Y-m-d');
            }
        }
        $bookedDates = array_values(array_unique($bookedDates));

        $data = array_merge($property->toArray(), [
            'rating' => $property->average_rating ?? 4.9,
            'instagram_videos' => collect($property->instagram_videos ?? [])->map(function ($video) {
                $imgPath = is_array($video) ? ($video['image'] ?? '') : '';
                return [
                    'image' => $imgPath ? url(Storage::url($imgPath)) : null,
                    'video_link' => is_array($video) ? ($video['video_link'] ?? '') : $video,
                ];
            }),
            'brochure' => $property->brochure ? url(Storage::url($property->brochure)) : null,
            'nearby_attractions' => collect($property->nearby_attractions ?? [])->map(function ($attr) {
                return [
                    'heading' => $attr['heading'] ?? '',
                    'description' => $attr['description'] ?? '',
                    'image' => (isset($attr['image']) && $attr['image']) ? url(Storage::url($attr['image'])) : null,
                    'alt_text' => $attr['alt_text'] ?? '',
                ];
            }),
            'gallery' => $formattedGallery,
            'amenities' => $formattedAmenities,
            'cancellation_rules' => $property->cancellationRules->map(function ($rule) {
                return [
                    'days_before' => $rule->days_before,
                    'deduction_percentage' => $rule->deduction_percentage
                ];
            }),
            'booked_dates' => $bookedDates,
            'category' => [
                'id' => $property->category->id ?? null,
                'name' => $property->category->name ?? null,
                'slug' => $property->category->slug ?? null,
            ],
            'destination' => [
                'id' => $property->destination->id ?? null,
                'name' => $property->destination->name ?? null,
                'slug' => $property->destination->slug ?? null,
            ],
            'vendor' => [
                'name' => $property->vendor->user->name ?? null,
                'email' => $property->vendor->user->email ?? null,
            ],
            // Meals collected from all active rooms (unique, flat)
            'meals' => $property->rooms
                ->where('is_active', true)
                ->flatMap(fn($r) => $r->meals ?? [])
                ->unique()
                ->values(),
            'rooms' => $property->rooms->where('is_active', true)->values()->map(function ($room) {
                $images = collect($room->images ?? [])->map(function ($img) {
                    $imgPath = is_array($img) ? ($img['path'] ?? '') : $img;
                    $imgAlt = is_array($img) ? ($img['alt'] ?? '') : '';
                    return [
                        'path' => $imgPath ? url(Storage::url($imgPath)) : null,
                        'alt' => $imgAlt,
                    ];
                })->filter(fn($i) => $i['path'] !== null)->values();

                return [
                    'id' => $room->id,
                    'title' => $room->title,
                    'description' => $room->description,
                    'bed_type' => $room->bed_type,
                    'images' => $images,
                ];
            }),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    #[OA\Get(path: "/api/properties/{slug}/related", summary: "Get related properties by slug", tags: ["Properties"])]
    #[OA\Parameter(name: "slug", in: "path", required: true, description: "The slug of the property", schema: new OA\Schema(type: "string"))]
    #[OA\Response(
        response: 200,
        description: "List of related properties",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "status", type: "string", example: "success"),
                new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object"))
            ]
        )
    )]
    public function related($slug)
    {
        $property = Property::where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!$property) {
            return response()->json([
                'status' => 'error',
                'message' => 'Property not found'
            ], 404);
        }

        // Fetch related properties: same destination or same category, excluding the current one
        $relatedProperties = Property::where('status', 'active')
            ->where('id', '!=', $property->id)
            ->where(function ($query) use ($property) {
                $query->where('destination_id', $property->destination_id)
                    ->orWhere('category_id', $property->category_id);
            })
            ->with(['category', 'destination.state', 'roomTypes'])
            ->limit(4)
            ->get();

        $formatted = $relatedProperties->map(function ($prop) {
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

            return array_merge($prop->toArray(), [
                'image' => $mainImage,
                'images' => $galleryImages,
                'category_name' => $prop->category->name ?? null,
                'destination_name' => $prop->destination->name ?? null,
                'state_name' => $prop->destination->state->name ?? $prop->state,
                'price' => $prop->amount  ?? '0.00',
                'rating' => $prop->average_rating ?? '4.90',
            ]);
        });

        return response()->json([
            'status' => 'success',
            'data' => $formatted
        ]);
    }
}
