<?php

namespace Database\Seeders;

use App\Models\PropertyCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertyCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Villa', 'icon' => 'home'],
            ['name' => 'Apartment', 'icon' => 'building'],
            ['name' => 'Homestay', 'icon' => 'user'],
            ['name' => 'B&B', 'icon' => 'coffee'],
            ['name' => 'Resort', 'icon' => 'umbrella'],
        ];

        foreach ($categories as $cat) {
            PropertyCategory::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'icon' => $cat['icon'],
            ]);
        }
    }
}
