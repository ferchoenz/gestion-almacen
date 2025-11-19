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
        Schema::create('material_outputs', function (Blueprint $table) {
            $table->id();

            // --- CAMPOS DE AUDITORÍA Y MULTI-TENANT ---
            // Conexión con la Terminal (TRP o TRVM)
            $table->foreignId('terminal_id')->constrained('terminals');
            // Quién registró la salida (el almacenista logueado)
            $table->foreignId('user_id')->constrained('users');

            // --- CAMPOS DEL FORMULARIO ---
            $table->enum('material_type', ['CONSUMIBLE', 'SPARE_PART']);
            $table->string('item_number')->nullable(); // Nulo si es consumible
            $table->string('description');
            $table->date('output_date'); // Fecha de salida
            $table->decimal('quantity', 10, 2); // 10 dígitos total, 2 decimales

            // --- Persona que recibe ---
            $table->string('receiver_name');
            // Usamos TEXT para guardar la firma en formato Base64 (es un texto largo)
            $table->text('receiver_signature');

            // --- Persona que entrega (Almacenista) ---
            // Ya tenemos el 'user_id' de quién lo registró, pero guardamos la firma
            $table->text('deliverer_signature');

            // --- CAMPOS DE WORKFLOW (Se llenan después) ---
            $table->string('work_order')->nullable(); // Orden de Trabajo (OT)
            $table->string('sap_confirmation')->nullable();

            // El status que definimos para nuestro Dashboard de pendientes
            $table->enum('status', ['PENDIENTE_OT', 'PENDIENTE_SAP', 'COMPLETO']);

            $table->string('pdf_vale_path')->nullable(); // Ruta al PDF "Vale de Salida"
            $table->timestamps(); // created_at y updated_at
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_outputs');
    }
};
