<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Auth;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'vendor_id' => 'required|exists:vendors,id',
            'category_id' => 'required|exists:property_categories,id',
            'destination_id' => 'required|exists:destinations,id',
            'name' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'total_bedrooms' => 'required|integer|min:0',
            'total_bathrooms' => 'required|integer|min:0',
            'max_guests' => 'required|integer|min:0',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'check_in_time' => 'nullable',
            'check_out_time' => 'nullable',
            'status' => 'required|in:pending,active,inactive',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'show_on_homepage'        => 'nullable|boolean',
            'show_in_menu'            => 'nullable|boolean',
            'gst'                     => 'nullable|numeric|min:0',
            'extra_person_charge'     => 'nullable|numeric|min:0',
            'instagram_video_links'   => 'nullable|array',
            'instagram_video_links.*' => 'nullable|url',
            'instagram_video_images'  => 'nullable|array',
            'instagram_video_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // Nearby Attractions
            'attraction_images.*'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'attraction_headings.*'   => 'nullable|string|max:255',
            'attraction_alts.*'       => 'nullable|string|max:255',
            'attraction_descriptions.*' => 'nullable|string',
            'brochure'                => 'nullable|file|mimes:pdf|max:10240',
            // Inline rooms
            'rooms'                   => 'nullable|array',
            'rooms.*.title'           => 'required_with:rooms.*|string|max:255',
            'rooms.*.description'     => 'nullable|string',
            'rooms.*.bed_type'        => 'nullable|string|max:255',
            'rooms.*.meals'           => 'nullable|array',
            'room_images.*.*'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'room_image_alts.*.*'     => 'nullable|string|max:255',
        ];

        if (Auth::check() && Auth::user()->role === 'vendor') {
            $rules['vendor_id'] = 'nullable';
            $rules['status'] = 'nullable';
        }

        return $rules;
    }
}
