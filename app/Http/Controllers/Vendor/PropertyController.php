<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Collection;
use App\Models\PropertyCategory;
use App\Models\Amenity;
use App\Models\Destination;
use App\Models\Room;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::with(['category', 'destination'])
            ->where('vendor_id', Auth::user()->vendor->id)
            ->latest()
            ->paginate(10);
        return view('vendor.properties.index', compact('properties'));
    }

    public function create()
    {
        $categories = PropertyCategory::all();
        $amenities = Amenity::all();
        $destinations = Destination::all();
        $collections = Collection::all();
        return view('vendor.properties.create', compact('categories', 'amenities', 'destinations', 'collections'));
    }

    public function store(StorePropertyRequest $request)
    {
        $data = $request->validated();
        $vendorId = Auth::user()->vendor->id;
        $data['vendor_id'] = $vendorId;

        $slug = Str::slug($request->name);
        $count = 1;
        $originalSlug = $slug;
        while (Property::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        $data['slug'] = $slug;
        $data['status'] = 'pending'; // Vendor added properties are pending by default

        // Handle Gallery
        $gallery = [];
        if ($request->hasFile('images')) {
            $alts = $request->input('alts', []);
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties/gallery', 'public');
                $gallery[] = [
                    'image' => $path,
                    'alt' => $alts[$index] ?? $request->name
                ];
            }
        }
        $data['gallery'] = $gallery;
        // Handle Instagram Videos (Array of Objects: image, video_link)
        $instagramVideos = [];
        if ($request->has('instagram_video_links')) {
            foreach ($request->instagram_video_links as $i => $link) {
                if (empty($link)) continue;
                $imgPath = null;
                if ($request->hasFile("instagram_video_images.$i")) {
                    $imgPath = $request->file("instagram_video_images.$i")->store('properties/instagram', 'public');
                }
                $instagramVideos[] = [
                    'image' => $imgPath,
                    'video_link' => $link
                ];
            }
        }
        $data['instagram_videos'] = $instagramVideos;
        
        // Handle Nearby Attractions
        $attractions = [];
        if ($request->has('attraction_headings')) {
            foreach ($request->attraction_headings as $i => $heading) {
                if (empty($heading)) continue;
                $imgPath = null;
                if ($request->hasFile("attraction_images.$i")) {
                    $imgPath = $request->file("attraction_images.$i")->store('properties/attractions', 'public');
                }
                $attractions[] = [
                    'heading' => $heading,
                    'image' => $imgPath,
                    'alt_text' => $request->attraction_alts[$i] ?? '',
                    'description' => $request->attraction_descriptions[$i] ?? '',
                ];
            }
        }
        $data['nearby_attractions'] = $attractions;

        if ($request->hasFile('brochure')) {
            $data['brochure'] = $request->file('brochure')->store('properties/brochures', 'public');
        }

        $property = Property::create($data);

        // Handle Cancellation Rules
        if ($request->has('cancellation_rules')) {
            foreach ($request->input('cancellation_rules', []) as $rule) {
                if (isset($rule['days_before']) && isset($rule['deduction_percentage'])) {
                    $property->cancellationRules()->create([
                        'days_before' => $rule['days_before'],
                        'deduction_percentage' => $rule['deduction_percentage']
                    ]);
                }
            }
        }

        // Handle Amenities
        if ($request->has('amenities')) {
            $property->amenities()->sync($request->amenities);
        }

        // Handle Collections
        if ($request->has('collections')) {
            $property->collections()->sync($request->collections);
        }

        // Handle Meals Relation
        if ($request->has('meals')) {
            $mealNames = $request->input('meals', []);
            $mealIds = \App\Models\Meal::whereIn('name', $mealNames)->pluck('id');
            $property->mealTypes()->sync($mealIds);
        }

        // Handle inline Rooms
        if ($request->has('rooms')) {
            foreach ($request->input('rooms', []) as $idx => $roomData) {
                if (empty($roomData['title'])) continue;

                $roomImages = [];
                if ($request->hasFile("room_images.$idx")) {
                    $alts = $request->input("room_image_alts.$idx", []);
                    foreach ($request->file("room_images.$idx") as $imgIdx => $image) {
                        $roomImages[] = [
                            'path' => $image->store('rooms/gallery', 'public'),
                            'alt'  => $alts[$imgIdx] ?? '',
                        ];
                    }
                }

                Room::create([
                    'property_id' => $property->id,
                    'title'       => $roomData['title'],
                    'description' => $roomData['description'] ?? null,
                    'bed_type'    => $roomData['bed_type'] ?? null,
                    'images'      => $roomImages,
                    'meals'       => $roomData['meals'] ?? [],
                    'is_active'   => true,
                ]);
            }
        }

        return redirect()->route('vendor.properties')->with('success', 'Property submitted for moderation.');
    }

    public function edit(Property $property)
    {
        if ($property->vendor_id !== Auth::user()->vendor->id) {
            abort(403);
        }
        $categories = PropertyCategory::all();
        $amenities = Amenity::all();
        $destinations = Destination::all();
        $collections = Collection::all();
        $property->load(['amenities', 'collections']);
        return view('vendor.properties.edit', compact('property', 'categories', 'amenities', 'destinations', 'collections'));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        if ($property->vendor_id !== Auth::user()->vendor->id) {
            abort(403);
        }
        $data = $request->validated();
        $data['vendor_id'] = $property->vendor_id;

        $slug = Str::slug($request->name);
        $count = 1;
        $originalSlug = $slug;
        while (Property::where('slug', $slug)->where('id', '!=', $property->id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        $data['slug'] = $slug;
        // Handle Instagram Videos (Array of Objects: image, video_link)
        $instagramVideos = [];
        if ($request->has('instagram_video_links')) {
            $existingInstaImages = $request->input('existing_instagram_video_images', []);
            foreach ($request->instagram_video_links as $i => $link) {
                if (empty($link)) continue;
                $imgPath = $existingInstaImages[$i] ?? null;
                if ($request->hasFile("instagram_video_images.$i")) {
                    if ($imgPath) {
                        Storage::disk('public')->delete($imgPath);
                    }
                    $imgPath = $request->file("instagram_video_images.$i")->store('properties/instagram', 'public');
                }
                $instagramVideos[] = [
                    'image' => $imgPath,
                    'video_link' => $link
                ];
            }
        }
        $data['instagram_videos'] = $instagramVideos;

        // Handle Nearby Attractions
        $attractions = [];
        if ($request->has('attraction_headings')) {
            $existingAttrImages = $request->input('existing_attraction_images', []);
            foreach ($request->attraction_headings as $i => $heading) {
                if (empty($heading)) continue;
                $imgPath = $existingAttrImages[$i] ?? null;
                if ($request->hasFile("attraction_images.$i")) {
                    $imgPath = $request->file("attraction_images.$i")->store('properties/attractions', 'public');
                }
                $attractions[] = [
                    'heading' => $heading,
                    'image' => $imgPath,
                    'alt_text' => $request->attraction_alts[$i] ?? '',
                    'description' => $request->attraction_descriptions[$i] ?? '',
                ];
            }
        }
        $data['nearby_attractions'] = $attractions;

        if ($request->hasFile('brochure')) {
            if ($property->brochure) {
                Storage::disk('public')->delete($property->brochure);
            }
            $data['brochure'] = $request->file('brochure')->store('properties/brochures', 'public');
        }

        $property->update($data);

        // Handle Cancellation Rules
        if ($request->has('cancellation_rules')) {
            $property->cancellationRules()->delete();
            foreach ($request->input('cancellation_rules', []) as $rule) {
                if (isset($rule['days_before']) && isset($rule['deduction_percentage'])) {
                    $property->cancellationRules()->create([
                        'days_before' => $rule['days_before'],
                        'deduction_percentage' => $rule['deduction_percentage']
                    ]);
                }
            }
        }

        $property->amenities()->sync($request->amenities ?? []);
        $property->collections()->sync($request->collections ?? []);

        // Handle Meals Relation
        $mealNames = $request->input('meals', []);
        $mealIds = \App\Models\Meal::whereIn('name', $mealNames)->pluck('id');
        $property->mealTypes()->sync($mealIds);

        // Handle Gallery
        $gallery = $property->gallery ?? [];
        if ($request->has('existing_alts')) {
            foreach ($request->existing_alts as $index => $alt) {
                if (isset($gallery[$index])) {
                    if (is_array($gallery[$index])) {
                        $gallery[$index]['alt'] = $alt;
                    } else {
                        $gallery[$index] = ['image' => $gallery[$index], 'alt' => $alt];
                    }
                }
            }
        }
        if ($request->hasFile('images')) {
            $alts = $request->input('alts', []);
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties/gallery', 'public');
                $gallery[] = ['image' => $path, 'alt' => $alts[$index] ?? $property->name];
            }
        }
        $property->update(['gallery' => $gallery]);

        return redirect()->route('vendor.properties')->with('success', 'Property updated successfully.');
    }

    public function deleteImage(Request $request, Property $property)
    {
        if ($property->vendor_id !== Auth::user()->vendor->id) {
            abort(403);
        }
        $path = $request->path;
        $gallery = $property->gallery ?? [];
        $newGallery = array_filter($gallery, function($item) use ($path) {
            $itemPath = is_array($item) ? ($item['image'] ?? '') : $item;
            return $itemPath !== $path;
        });
        if (count($gallery) !== count($newGallery)) {
            Storage::disk('public')->delete($path);
            $property->update(['gallery' => array_values($newGallery)]);
        }
        return response()->json(['success' => true]);
    }

    public function ajaxAddSpecialDates(\Illuminate\Http\Request $request, Property $property)
    {
        if ($property->vendor_id !== Auth::user()->vendor->id) {
            abort(403);
        }

        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'amount' => 'required|numeric|min:0',
            'label' => 'nullable|string|max:255',
            'is_open' => 'nullable|boolean'
        ]);

        $fromDate = \Carbon\Carbon::parse($request->from_date);
        $toDate = \Carbon\Carbon::parse($request->to_date);
        $amount = $request->amount;
        $label = $request->label;
            $property->mealTypes()->sync($mealIds);
        }

        // Handle inline Rooms
        if ($request->has('rooms')) {
            foreach ($request->input('rooms', []) as $idx => $roomData) {
                if (empty($roomData['title'])) continue;

                $roomImages = [];
                if ($request->hasFile("room_images.$idx")) {
                    $alts = $request->input("room_image_alts.$idx", []);
                    foreach ($request->file("room_images.$idx") as $imgIdx => $image) {
                        $roomImages[] = [
                            'path' => $image->store('rooms/gallery', 'public'),
                            'alt'  => $alts[$imgIdx] ?? '',
                        ];
                    }
                }

                Room::create([
                    'property_id' => $property->id,
                    'title'       => $roomData['title'],
                    'description' => $roomData['description'] ?? null,
                    'bed_type'    => $roomData['bed_type'] ?? null,
                    'images'      => $roomImages,
                    'meals'       => $roomData['meals'] ?? [],
                    'is_active'   => true,
                ]);
            }
        }

        return redirect()->route('vendor.properties')->with('success', 'Property submitted for moderation.');
    }

    public function edit(Property $property)
    {
        if ($property->vendor_id !== Auth::user()->vendor->id) {
            abort(403);
        }
        $categories = PropertyCategory::all();
        $amenities = Amenity::all();
        $destinations = Destination::all();
        $collections = Collection::all();
        $property->load(['amenities', 'collections']);
        return view('vendor.properties.edit', compact('property', 'categories', 'amenities', 'destinations', 'collections'));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        if ($property->vendor_id !== Auth::user()->vendor->id) {
            abort(403);
        }
        $data = $request->validated();
        $data['vendor_id'] = $property->vendor_id;

        $slug = Str::slug($request->name);
        $count = 1;
        $originalSlug = $slug;
        while (Property::where('slug', $slug)->where('id', '!=', $property->id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        $data['slug'] = $slug;
        // Handle Instagram Videos (Array of Objects: image, video_link)
        $instagramVideos = [];
        if ($request->has('instagram_video_links')) {
            $existingInstaImages = $request->input('existing_instagram_video_images', []);
            foreach ($request->instagram_video_links as $i => $link) {
                if (empty($link)) continue;
                $imgPath = $existingInstaImages[$i] ?? null;
                if ($request->hasFile("instagram_video_images.$i")) {
                    if ($imgPath) {
                        Storage::disk('public')->delete($imgPath);
                    }
                    $imgPath = $request->file("instagram_video_images.$i")->store('properties/instagram', 'public');
                }
                $instagramVideos[] = [
                    'image' => $imgPath,
                    'video_link' => $link
                ];
            }
        }
        $data['instagram_videos'] = $instagramVideos;

        // Handle Nearby Attractions
        $attractions = [];
        if ($request->has('attraction_headings')) {
            $existingAttrImages = $request->input('existing_attraction_images', []);
            foreach ($request->attraction_headings as $i => $heading) {
                if (empty($heading)) continue;
                $imgPath = $existingAttrImages[$i] ?? null;
                if ($request->hasFile("attraction_images.$i")) {
                    $imgPath = $request->file("attraction_images.$i")->store('properties/attractions', 'public');
                }
                $attractions[] = [
                    'heading' => $heading,
                    'image' => $imgPath,
                    'alt_text' => $request->attraction_alts[$i] ?? '',
                    'description' => $request->attraction_descriptions[$i] ?? '',
                ];
            }
        }
        $data['nearby_attractions'] = $attractions;

        if ($request->hasFile('brochure')) {
            if ($property->brochure) {
                Storage::disk('public')->delete($property->brochure);
            }
            $data['brochure'] = $request->file('brochure')->store('properties/brochures', 'public');
        }

        $property->update($data);

        // Handle Cancellation Rules
        if ($request->has('cancellation_rules')) {
            $property->cancellationRules()->delete();
            foreach ($request->input('cancellation_rules', []) as $rule) {
                if (isset($rule['days_before']) && isset($rule['deduction_percentage'])) {
                    $property->cancellationRules()->create([
                        'days_before' => $rule['days_before'],
                        'deduction_percentage' => $rule['deduction_percentage']
                    ]);
                }
            }
        }

        $property->amenities()->sync($request->amenities ?? []);
        $property->collections()->sync($request->collections ?? []);

        // Handle Meals Relation
        $mealNames = $request->input('meals', []);
        $mealIds = \App\Models\Meal::whereIn('name', $mealNames)->pluck('id');
        $property->mealTypes()->sync($mealIds);

        // Handle Gallery
        $gallery = $property->gallery ?? [];
        if ($request->has('existing_alts')) {
            foreach ($request->existing_alts as $index => $alt) {
                if (isset($gallery[$index])) {
                    if (is_array($gallery[$index])) {
                        $gallery[$index]['alt'] = $alt;
                    } else {
                        $gallery[$index] = ['image' => $gallery[$index], 'alt' => $alt];
                    }
                }
            }
        }
        if ($request->hasFile('images')) {
            $alts = $request->input('alts', []);
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties/gallery', 'public');
                $gallery[] = ['image' => $path, 'alt' => $alts[$index] ?? $property->name];
            }
        }
        $property->update(['gallery' => $gallery]);

        return redirect()->route('vendor.properties')->with('success', 'Property updated successfully.');
    }

    public function deleteImage(Request $request, Property $property)
    {
        if ($property->vendor_id !== Auth::user()->vendor->id) {
            abort(403);
        }
        $path = $request->path;
        $gallery = $property->gallery ?? [];
        $newGallery = array_filter($gallery, function($item) use ($path) {
            $itemPath = is_array($item) ? ($item['image'] ?? '') : $item;
            return $itemPath !== $path;
        });
        if (count($gallery) !== count($newGallery)) {
            Storage::disk('public')->delete($path);
            $property->update(['gallery' => array_values($newGallery)]);
        }
        return response()->json(['success' => true]);
    }

    public function ajaxAddSpecialDates(\Illuminate\Http\Request $request, Property $property)
    {
        if ($property->vendor_id !== Auth::user()->vendor->id) {
            abort(403);
        }

        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'amount' => 'required|numeric|min:0',
            'label' => 'nullable|string|max:255',
            'is_open' => 'nullable|boolean'
        ]);

        $fromDate = \Carbon\Carbon::parse($request->from_date);
        $toDate = \Carbon\Carbon::parse($request->to_date);
        $amount = $request->amount;
        $label = $request->label;
        $isOpen = $request->has('is_open') ? $request->is_open : 1;
        $days = $request->input('days', []);

        $addedDates = [];

        for ($date = $fromDate; $date->lte($toDate); $date->addDay()) {
            if (!empty($days) && !in_array($date->dayOfWeek, $days)) {
                continue;
            }

            $dateStr = $date->format('Y-m-d');
            $sd = $property->specialDates()->updateOrCreate(
                ['date' => $dateStr],
                ['amount' => $amount, 'is_open' => $isOpen, 'label' => $label]
            );
            $addedDates[] = $sd;
        }

        return response()->json([
            'success' => true,
            'message' => 'Special dates added successfully.',
            'dates' => $addedDates
        ]);
    }

    public function calendar(Property $property)
    {
        if ($property->vendor_id !== Auth::user()->vendor->id) {
            abort(403);
        }
        return view('vendor.properties.calendar', compact('property'));
    }

    public function getCalendarEvents(\Illuminate\Http\Request $request, Property $property)
    {
        if ($property->vendor_id !== Auth::user()->vendor->id) {
            return response()->json([], 403);
        }
        
        $startStr = $request->query('start');
        $endStr = $request->query('end');

        if (!$startStr || !$endStr) {
            return response()->json([]);
        }

        $startDate = \Carbon\Carbon::parse($startStr);
        $endDate = \Carbon\Carbon::parse($endStr);
        $basePrice = $property->amount ?? 0;

        $events = [];

        // 1. Base Prices
        for ($date = $startDate->copy(); $date->lt($endDate); $date->addDay()) {
            $events[$date->format('Y-m-d')] = [
                'title' => 'Available ₹' . number_format($basePrice, 0),
                'start' => $date->format('Y-m-d'),
                'display' => 'background',
                'color' => '#f8fafc',
                'textColor' => '#1e293b',
                'amount' => $basePrice
            ];
        }

        // 2. Special Dates Override
        $specialDates = $property->specialDates()
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        foreach ($specialDates as $sd) {
            $dateStr = $sd->date->format('Y-m-d');
            $title = 'Available ₹' . number_format($sd->amount, 0);
            if (!$sd->is_open) {
                $title = 'Closed';
            }
            if ($sd->label) {
                $title .= ' (' . $sd->label . ')';
            }

            $events[$dateStr] = [
                'title' => $title,
                'start' => $dateStr,
                'display' => 'background',
                'color' => $sd->is_open ? '#e0e7ff' : '#fee2e2',
                'textColor' => $sd->is_open ? '#4338ca' : '#b91c1c',
                'amount' => $sd->amount
            ];
        }

        // 3. Bookings
        $bookings = $property->bookings()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in', [$startDate, $endDate])
                      ->orWhereBetween('check_out', [$startDate, $endDate]);
            })->get();

        $bookingEvents = [];
        foreach ($bookings as $booking) {
            $bookingEvents[] = [
                'title' => $booking->customer_name ?? 'Booked',
                'start' => $booking->check_in,
                'end' => $booking->check_out, // exclusive in FullCalendar
                'color' => '#3b82f6',
                'textColor' => '#ffffff'
            ];
        }

        return response()->json(array_merge(array_values($events), $bookingEvents));
    }
}
