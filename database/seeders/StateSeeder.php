<?php

namespace Database\Seeders;

use App\Models\State;
use App\Models\Destination;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Maharashtra' => ['Lonavala', 'Mahabaleshwar', 'Panchgani', 'Panvel', 'Khopoli', 'Murbad', 'Igatpuri', 'Karjat', 'Alibaug'],
            'Goa' => ['North Goa', 'South Goa', 'Anjuna', 'Calangute'],
            'Karnataka' => ['Coorg', 'Chikmagalur', 'Nandi Hills', 'Mysore'],
            'Himachal Pradesh' => ['Manali', 'Shimla', 'Kullu', 'Kasol'],
            'Delhi' => ['New Delhi', 'South Delhi']
        ];

        foreach ($data as $stateName => $destinations) {
            $state = State::firstOrCreate([
                'slug' => Str::slug($stateName)
            ], [
                'name' => $stateName,
            ]);

            foreach ($destinations as $destName) {
                Destination::updateOrCreate([
                    'slug' => Str::slug($destName)
                ], [
                    'name' => $destName,
                    'state_id' => $state->id,
                    'status' => 'active',
                    'show_in_menu' => true,
                    'is_best_rate' => true
                ]);
            }
        }
    }
}
