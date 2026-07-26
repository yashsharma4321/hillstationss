<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\Destination;
use App\Models\PropertyCategory;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertyDataSeeder extends Seeder
{
    public function run(): void
    {
        $destination = Destination::where('slug', 'lonavala')->first();
        $category = PropertyCategory::where('slug', 'villa')->first();
        $vendor = Vendor::first();

        if (!$destination || !$category || !$vendor) {
            return;
        }

        $properties = [
            [
                'name' => 'Luxurious 3 BHK Villa in Lonavala',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'guests' => 9,
                'price' => 12000,
            ],
            [
                'name' => 'Luxurious 4 BHK Villa in Lonavala',
                'bedrooms' => 4,
                'bathrooms' => 3,
                'guests' => 12,
                'price' => 18000,
            ],
        ];

        foreach ($properties as $p) {
            $slug = Str::slug($p['name'] . '-' . Str::random(8));
            $property = Property::create([
                'vendor_id' => $vendor->id,
                'category_id' => $category->id,
                'destination_id' => $destination->id,
                'name' => $p['name'],
                'slug' => $slug,
                'total_bedrooms' => $p['bedrooms'],
                'total_bathrooms' => $p['bathrooms'],
                'max_guests' => $p['guests'],
                'city' => 'Lonavala',
                'state' => 'Maharashtra',
                'country' => 'India',
                'latitude' => '18.7546',
                'longitude' => '73.4062',
                'status' => 'active',
                'show_in_menu' => true,
                'gallery' => [
                    ['image' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&q=80&w=600', 'alt' => 'Main View']
                ]
            ]);

            // Create a room type for price calculation
            $property->roomTypes()->create([
                'name' => 'Full Villa',
                'base_price' => $p['price'],
                'total_units' => 1,
            ]);
        }
    }
}
