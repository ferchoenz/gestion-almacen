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
        Schema::create('inventory_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terminal_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique(); // Ej: "A1-E3" (Pasillo A1, Estante 3)
            $table->string('name'); // Nombre descriptivo
            $table->string('aisle')->nullable(); // Pasillo
            $table->string('rack')->nullable(); // Estante
            $table->string('level')->nullable(); // Nivel
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_locations');
    }
};
