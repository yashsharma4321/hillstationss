<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountHead;

class SupplementalAccountSeeder extends Seeder
{
    public function run(): void
    {
        // First correct existing E-3001 to revenue instead of equity for better P&L representation
        AccountHead::where('code', 'E-3001')->update(['type' => 'revenue', 'name' => 'Commission Revenue']);

        $accounts = [
            // Revenue
            ['name' => 'Other Revenue', 'code' => 'R-4002', 'type' => 'revenue', 'is_system' => true],
            
            // Expenses
            ['name' => 'Tax Expense (GST)', 'code' => 'X-5001', 'type' => 'expense', 'is_system' => true],
            ['name' => 'Server & Maintenance', 'code' => 'X-5002', 'type' => 'expense', 'is_system' => true],
            ['name' => 'Payment Gateway Fees', 'code' => 'X-5003', 'type' => 'expense', 'is_system' => true],
            ['name' => 'Marketing Expense', 'code' => 'X-5004', 'type' => 'expense', 'is_system' => true],
        ];

        foreach ($accounts as $index => $acc) {
            AccountHead::updateOrCreate(['code' => $acc['code']], $acc);
        }
    }
}
