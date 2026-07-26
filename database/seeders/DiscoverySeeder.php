<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\RoomType;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DiscoverySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure we have a Category
        $category = PropertyCategory::where('name', 'Villa')->first() ?? PropertyCategory::create([
            'name' => 'Villa', 'slug' => 'villa', 'icon' => 'home'
        ]);

        // 2. Ensure we have a Vendor
        $vendorUser = User::where('email', 'vendor@test.com')->first() ?? User::create([
            'name' => 'Test Vendor',
            'email' => 'vendor@test.com',
            'password' => bcrypt('password'),
            'role' => 'vendor'
        ]);

        $vendor = Vendor::where('user_id', $vendorUser->id)->first() ?? Vendor::create([
            'user_id' => $vendorUser->id,
            'business_name' => 'Dream Villas',
            'business_phone' => '1234567890',
            'business_email' => 'vendor@test.com',
            'status' => 'approved'
        ]);

        // 3. Create Destinations
        $destinationData = [
            ['name' => 'Panchgani', 'is_best_rate' => 1],
            ['name' => 'Mahabaleshwar', 'is_best_rate' => 1],
            ['name' => 'Lonavala', 'is_best_rate' => 1],
        ];

        foreach ($destinationData as $d) {
            $dest = Destination::updateOrCreate(
                ['slug' => Str::slug($d['name'])],
                [
                    'name' => $d['name'],
                    'is_best_rate' => $d['is_best_rate'],
                    'status' => 'active',
                    'show_on_homepage' => 1
                ]
            );

            // 4. Create Properties (3 BHK, 4 BHK, 6 BHK)
            $bhkOptions = [
                ['bhk' => 3, 'price' => 12000],
                ['bhk' => 4, 'price' => 18000],
                ['bhk' => 6, 'price' => 25000],
            ];

            foreach ($bhkOptions as $opt) {
                $prop = Property::create([
                    'vendor_id' => $vendor->id,
                    'category_id' => $category->id,
                    'destination_id' => $dest->id,
                    'name' => "Luxurious " . $opt['bhk'] . " BHK Villa in " . $dest->name,
                    'slug' => Str::slug("Luxurious " . $opt['bhk'] . " BHK Villa in " . $dest->name . "-" . uniqid()),
                    'description' => "A beautiful villa perfect for families.",
                    'total_bedrooms' => $opt['bhk'],
                    'total_bathrooms' => $opt['bhk'] - 1,
                    'max_guests' => $opt['bhk'] * 3,
                    'city' => $dest->name,
                    'state' => 'Maharashtra',
                    'country' => 'India',
                    'status' => 'active',
                    'average_rating' => 4.9,
                    'show_on_homepage' => 1,
                    'is_featured' => 1,
                    'gallery' => [
                        'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&q=80&w=600'
                    ]
                ]);

                // 5. Create Room Type with Price
                RoomType::create([
                    'property_id' => $prop->id,
                    'name' => 'Entire Villa',
                    'base_price' => $opt['price'],
                    'max_guests' => $prop->max_guests,
                    'total_units' => 1
                ]);
            }
        }
    }
}
