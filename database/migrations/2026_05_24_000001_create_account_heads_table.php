<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_heads', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // e.g., Cash, Revenue, Vendor Payable
            $table->enum('type', ['asset', 'liability', 'equity']); // COA types
            $table->string('code')->unique();              // e.g., A001, L001, E001
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);  // System accounts can't be deleted
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_heads');
    }
};
