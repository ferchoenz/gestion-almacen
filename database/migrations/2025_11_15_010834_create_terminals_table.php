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
    // Esto crea la tabla 'terminals' con dos columnas: id y name
    Schema::create('terminals', function (Blueprint $table) {
        $table->id(); // ID numérico autoincremental
        $table->string('name')->unique(); // 'TRP', 'TRVM' (unique = no se puede repetir)
        $table->timestamps(); // Columnas 'created_at' y 'updated_at' (útil para auditoría)
    });
}

/**
 * Reverse the migrations.
 */
public function down(): void
{
    Schema::dropIfExists('terminals');
}
};
