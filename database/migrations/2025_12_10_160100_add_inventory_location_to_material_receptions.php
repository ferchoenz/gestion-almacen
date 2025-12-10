<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_receptions', function (Blueprint $table) {
            $table->foreignId('inventory_location_id')->nullable()->after('consumable_id')
                  ->constrained('inventory_locations')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('material_receptions', function (Blueprint $table) {
            $table->dropForeign(['inventory_location_id']);
            $table->dropColumn('inventory_location_id');
        });
    }
};
