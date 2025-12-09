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
        Schema::table('material_outputs', function (Blueprint $table) {
            $table->foreignId('consumable_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            
            // Índice para performance
            $table->index('consumable_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_outputs', function (Blueprint $table) {
            $table->dropForeign(['consumable_id']);
            $table->dropIndex(['consumable_id']);
            $table->dropColumn('consumable_id');
        });
    }
};
