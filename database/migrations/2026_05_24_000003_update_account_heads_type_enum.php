<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Changing ENUM in Laravel requires raw SQL or doctrine/dbal. Raw SQL is safest here based on MySQL.
        DB::statement("ALTER TABLE account_heads MODIFY COLUMN type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL");
    }

    public function down(): void
    {
        // Revert back. We won't safely be able to drop if rows use revenue/expense without deleting them first.
        DB::statement("ALTER TABLE account_heads MODIFY COLUMN type ENUM('asset', 'liability', 'equity') NOT NULL");
    }
};
