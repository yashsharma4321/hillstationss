<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Vendor User',
            'email' => 'vendor@example.com',
            'phone' => '0987654321',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'is_active' => true,
        ]);

        Vendor::create([
            'user_id' => $user->id,
            'business_name' => 'Paradise Stays',
            'business_phone' => '0987654321',
            'business_email' => 'contact@paradise.com',
            'commission_rate' => 10.00,
            'is_approved' => true,
            'kyc_status' => 'approved',
            'status' => 'active',
        ]);
    }
}
