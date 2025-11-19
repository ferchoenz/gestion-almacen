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
    // Esto crea la tabla 'roles'
    Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique(); // 'Administrador', 'Gerencia', 'Mantenimiento', etc.
        $table->timestamps();
    });
}

/**
 * Reverse the migrations.
 */
public function down(): void
{
    Schema::dropIfExists('roles');
}
};
