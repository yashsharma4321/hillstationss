<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Collection;
use App\Models\PropertyCategory;
use App\Models\Amenity;
use App\Models\Vendor;
use App\Models\Destination;
use App\Models\Room;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::with(['vendor', 'category', 'destination'])->latest()->paginate(10);
        return view('admin.properties.index', compact('properties'));
    }

    public function show(Property $property)
    {
        $property->load(['vendor', 'category', 'destination', 'amenities', 'images', 'collections', 'rooms']);
        return view('admin.properties.show', compact('property'));
    }

    public function create()
    {
        $categories = PropertyCategory::all();
        $amenities = Amenity::all();
        $destinations = Destination::all();
        $vendors = Vendor::with('user')->get();
        $collections = Collection::all();
        return view('admin.properties.create', compact('categories', 'amenities', 'destinations', 'vendors', 'collections'));
    }

    public function store(StorePropertyRequest $request)
    {
        $data = $request->validated();

        $slug = Str::slug($request->name);
        $count = 1;
        $originalSlug = $slug;
        while (Property::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
      
        $data['slug'] = $slug;
        $data['show_on_homepage'] = $request->has('show_on_homepage');
        $data['show_in_menu'] = $request->has('show_in_menu');
        // Handle Gallery (Array of Objects)
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

        // Handle Special Dates
        if ($request->has('special_dates')) {
            $property->specialDates()->delete();
            foreach ($request->input('special_dates', []) as $sd) {
                if (empty($sd['date'])) continue;
                $property->specialDates()->create([
                    'date'   => $sd['date'],
                    'amount' => $sd['amount'] ?? 0, 'is_open' => $sd['is_open'] ?? 1,
                    'label'  => $sd['label'] ?? null,
                ]);
            }
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

        return redirect()->route('admin.properties.index')->with('success', 'Property added successfully.');
    }

    public function edit(Property $property)
    {
        $categories = PropertyCategory::all();
        $amenities = Amenity::all();
        $destinations = Destination::all();
        $vendors = Vendor::with('user')->get();
        $collections = Collection::all();
        $property->load(['amenities', 'images', 'collections']);
        return view('admin.properties.edit', compact('property', 'categories', 'amenities', 'destinations', 'vendors', 'collections'));
    }

    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $data = $request->validated();

        $slug = Str::slug($request->name);
        $count = 1;
        $originalSlug = $slug;
        while (Property::where('slug', $slug)->where('id', '!=', $property->id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        $data['slug'] = $slug;
        $data['show_on_homepage'] = $request->has('show_on_homepage');
        $data['show_in_menu'] = $request->has('show_in_menu');
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

        // Handle Amenities
        $property->amenities()->sync($request->amenities ?? []);

        // Handle Collections
        $property->collections()->sync($request->collections ?? []);

        // Handle Meals Relation
        $mealNames = $request->input('meals', []);
        $mealIds = \App\Models\Meal::whereIn('name', $mealNames)->pluck('id');
        $property->mealTypes()->sync($mealIds);

        // Handle Special Dates
        $property->specialDates()->delete();
        foreach ($request->input('special_dates', []) as $sd) {
            if (empty($sd['date'])) continue;
            $property->specialDates()->create([
                'date'   => $sd['date'],
                'amount' => $sd['amount'] ?? 0, 'is_open' => $sd['is_open'] ?? 1,
                'label'  => $sd['label'] ?? null,
            ]);
        }

        // Handle Gallery
        $gallery = $property->gallery ?? [];
        
        // Update existing alts
        if ($request->has('existing_alts')) {
            foreach ($request->existing_alts as $index => $alt) {
                if (isset($gallery[$index])) {
                    if (is_array($gallery[$index])) {
                        $gallery[$index]['alt'] = $alt;
                    } else {
                        $gallery[$index] = [
                            'image' => $gallery[$index],
                            'alt' => $alt
                        ];
                    }
                }
            }
        }

        // Add new images
        if ($request->hasFile('images')) {
            $alts = $request->input('alts', []);
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties/gallery', 'public');
                $gallery[] = [
                    'image' => $path,
                    'alt' => $alts[$index] ?? $property->name
                ];
            }
        }

        $property->update([
            'gallery' => $gallery
        ]);

        return redirect()->route('admin.properties.index')->with('success', 'Property updated successfully.');
    }

    public function destroy(Property $property)
    {
        // Delete images from storage
        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->image_url);
        }

        $property->delete();

        return redirect()->route('admin.properties.index')->with('success', 'Property deleted successfully.');
    }

    public function deleteImage(Request $request, Property $property)
    {
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

    public function bulkAddSpecialDates(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'property_ids' => 'required|array',
            'property_ids.*' => 'exists:properties,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'days' => 'required|array',
            'amount' => 'nullable|numeric',
            'is_open' => 'required|boolean'
        ]);

        $properties = \App\Models\Property::whereIn('id', $request->property_ids)->get();
        $fromDate = \Carbon\Carbon::parse($request->from_date);
        $toDate = \Carbon\Carbon::parse($request->to_date);
        $days = $request->days;
        $amount = $request->amount ?? 0;
        $isOpen = $request->is_open;

        $datesToAdd = [];
        for ($date = $fromDate; $date->lte($toDate); $date->addDay()) {
            // Carbon dayOfWeek returns 0 (Sun) to 6 (Sat)
            if (in_array($date->dayOfWeek, $days)) {
                $datesToAdd[] = $date->format('Y-m-d');
            }
        }

        foreach ($properties as $property) {
            foreach ($datesToAdd as $dateStr) {
                $property->specialDates()->updateOrCreate(
                    ['date' => $dateStr],
                    ['amount' => $amount, 'is_open' => $isOpen]
                );
            }
        }

        return response()->json(['success' => true, 'message' => 'Special dates added successfully.']);
    }
}
