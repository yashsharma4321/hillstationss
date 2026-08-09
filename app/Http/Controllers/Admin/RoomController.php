<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index(Property $property)
    {
        $rooms = $property->rooms()->latest()->get();
        return view('admin.rooms.index', compact('rooms', 'property'));
    }

    public function create(Property $property)
    {
        return view('admin.rooms.create', compact('property'));
    }

    public function store(Request $request, Property $property)
    {
        $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'bed_type'           => 'nullable|string|max:255',
            'room_images.*'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'room_image_alts.*'  => 'nullable|string|max:255',
            'meals'              => 'nullable|array',
        ]);

        $images = [];
        if ($request->hasFile('room_images')) {
            $alts = $request->input('room_image_alts', []);
            foreach ($request->file('room_images') as $index => $image) {
                $images[] = [
                    'path' => $image->store('rooms/gallery', 'public'),
                    'alt'  => $alts[$index] ?? '',
                ];
            }
        }

        Room::create([
            'property_id' => $property->id,
            'title'       => $request->title,
            'description' => $request->description,
            'bed_type'    => $request->bed_type,
            'images'      => $images,
            'meals'       => $request->meals ?? [],
            'is_active'   => true,
        ]);

        return redirect()->route('admin.properties.rooms.index', $property)
            ->with('success', 'Room added successfully.');
    }

    public function edit(Room $room)
    {
        $property = $room->property;
        return view('admin.rooms.edit', compact('room', 'property'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'bed_type'           => 'nullable|string|max:255',
            'room_images.*'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'room_image_alts.*'  => 'nullable|string|max:255',
            'existing_alts.*'    => 'nullable|string|max:255',
            'meals'              => 'nullable|array',
        ]);

        // Start with existing images, normalise legacy plain-string entries
        $images = collect($room->images ?? [])->map(function ($img) {
            return is_array($img) ? $img : ['path' => $img, 'alt' => ''];
        })->values()->toArray();

        // Update alt texts for existing images
        if ($request->has('existing_alts')) {
            foreach ($request->existing_alts as $index => $alt) {
                if (isset($images[$index])) {
                    $images[$index]['alt'] = $alt;
                }
            }
        }

        // Append newly uploaded images
        if ($request->hasFile('room_images')) {
            $alts = $request->input('room_image_alts', []);
            foreach ($request->file('room_images') as $index => $image) {
                $images[] = [
                    'path' => $image->store('rooms/gallery', 'public'),
                    'alt'  => $alts[$index] ?? '',
                ];
            }
        }

        $room->update([
            'title'       => $request->title,
            'description' => $request->description,
            'bed_type'    => $request->bed_type,
            'images'      => $images,
            'meals'       => $request->meals ?? [],
            'is_active'   => $request->has('is_active'),
        ]);

        return redirect()->route('admin.properties.rooms.index', $room->property_id)
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        $propertyId = $room->property_id;

        // Safety guard: never allow deleting the property's last room
        // (the only way to delete the property is through the property delete action)
        $roomCount = Room::where('property_id', $propertyId)->count();
        if ($roomCount <= 1) {
            return redirect()->route('admin.properties.rooms.index', $propertyId)
                ->with('error', 'Cannot delete the last room. A property must have at least one room. Use the property delete option if you want to remove the property entirely.');
        }

        foreach ($room->images ?? [] as $image) {
            $path = is_array($image) ? ($image['path'] ?? '') : $image;
            if ($path) Storage::disk('public')->delete($path);
        }

        $room->delete();

        return redirect()->route('admin.properties.rooms.index', $propertyId)
            ->with('success', 'Room deleted successfully.');
    }

    public function deleteImage(Request $request, Room $room)
    {
        $path   = $request->path;
        $images = collect($room->images ?? [])->map(function ($img) {
            return is_array($img) ? $img : ['path' => $img, 'alt' => ''];
        });

        $newImages = $images->filter(fn($img) => ($img['path'] ?? '') !== $path)->values();

        if ($images->count() !== $newImages->count()) {
            Storage::disk('public')->delete($path);
            $room->update(['images' => $newImages->toArray()]);
        }

        return response()->json(['success' => true]);
    }
}
