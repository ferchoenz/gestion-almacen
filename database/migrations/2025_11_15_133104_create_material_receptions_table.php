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
        Schema::create('material_receptions', function (Blueprint $table) {
            $table->id();

            // --- CAMPOS DE AUDITORÍA Y MULTI-TENANT ---
            $table->foreignId('terminal_id')->constrained('terminals');
            $table->foreignId('user_id')->constrained('users'); // Quién registró

            // --- CAMPOS DEL FORMULARIO (Tus requisitos) ---
            $table->enum('material_type', ['CONSUMIBLE', 'SPARE_PART']);
            $table->string('item_number')->nullable(); // Nulo si es consumible
            $table->string('description');
            $table->string('provider'); // Proveedor
            $table->string('purchase_order'); // Orden de Compra (OC)
            $table->date('reception_date');
            $table->decimal('quantity', 10, 2);
            $table->boolean('quality_certificate'); // Certificado de Calidad SI/NO
            $table->string('sap_confirmation')->nullable(); // Nulo si es consumible

            // --- CAMPOS DE WORKFLOW ---
            $table->string('storage_location')->nullable(); // Ubicación (opcional)

            // Creamos un status basado en tu requisito de 'ubicación opcional'
            $table->enum('status', ['PENDIENTE_UBICACION', 'COMPLETO'])
                  ->default('PENDIENTE_UBICACION');

            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_receptions');
    }
};
