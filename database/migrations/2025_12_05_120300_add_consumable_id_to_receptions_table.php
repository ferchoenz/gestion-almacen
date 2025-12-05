<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('material_receptions', function (Blueprint $table) {
            // Add consumable_id to link receptions to consumables
            $table->foreignId('consumable_id')->nullable()->after('id')->constrained()->nullOnDelete();
            
            // Keep description field for legacy compatibility
            // If consumable_id is set, description will be ignored
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_receptions', function (Blueprint $table) {
            $table->dropForeign(['consumable_id']);
            $table->dropColumn('consumable_id');
        });
    }
};
