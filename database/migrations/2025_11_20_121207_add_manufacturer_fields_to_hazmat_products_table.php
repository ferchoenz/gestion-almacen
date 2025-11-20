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
        Schema::table('hazmat_products', function (Blueprint $table) {
            $table->string('manufacturer')->nullable()->after('cas_number');
            $table->string('emergency_phone')->nullable()->after('manufacturer');
            $table->text('address')->nullable()->after('emergency_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hazmat_products', function (Blueprint $table) {
            $table->dropColumn(['manufacturer', 'emergency_phone', 'address']);
        });
    }
};
