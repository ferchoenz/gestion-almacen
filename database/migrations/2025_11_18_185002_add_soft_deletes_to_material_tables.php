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
        // Agregar columnas a Salidas
        Schema::table('material_outputs', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable(); // Motivo
            $table->softDeletes(); // Columna deleted_at
        });

        // Agregar columnas a Recepciones
        Schema::table('material_receptions', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable(); // Motivo
            $table->softDeletes(); // Columna deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_outputs', function (Blueprint $table) {
            $table->dropColumn('cancellation_reason');
            $table->dropSoftDeletes();
        });
        Schema::table('material_receptions', function (Blueprint $table) {
            $table->dropColumn('cancellation_reason');
            $table->dropSoftDeletes();
        });
    }
};
