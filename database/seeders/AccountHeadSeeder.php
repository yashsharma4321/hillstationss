<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountHead;

class AccountHeadSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Assets
            ['name' => 'Cash in Hand', 'code' => 'A-1001', 'type' => 'asset', 'is_system' => true],
            ['name' => 'Bank Account', 'code' => 'A-1002', 'type' => 'asset', 'is_system' => true],
            ['name' => 'Accounts Receivable', 'code' => 'A-1003', 'type' => 'asset', 'is_system' => true],
            
            // Liabilities
            ['name' => 'Accounts Payable (Vendors)', 'code' => 'L-2001', 'type' => 'liability', 'is_system' => true],
            ['name' => 'Tax Payable', 'code' => 'L-2002', 'type' => 'liability', 'is_system' => true],
            
            // Equity / Income (Since we only have Equity for now based on user specs)
            ['name' => 'Commission Income', 'code' => 'E-3001', 'type' => 'equity', 'is_system' => true],
            ['name' => 'Owner Equity', 'code' => 'E-3002', 'type' => 'equity', 'is_system' => true],
        ];

        foreach ($accounts as $index => $acc) {
            AccountHead::updateOrCreate(['code' => $acc['code']], $acc);
        }
    }
}
