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
        Schema::create('inventory_exits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terminal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('consumable_id')->constrained()->cascadeOnDelete();
            
            // Información de la salida
            $table->decimal('quantity', 10, 2);
            $table->date('exit_date');
            
            // Destinatario y propósito
            $table->string('department')->nullable(); // Departamento solicitante
            $table->string('recipient_name'); // Persona que recibe
            $table->string('purpose')->nullable(); // Propósito del consumo
            $table->text('notes')->nullable(); // Notas adicionales
            
            // Documento de referencia (opcional)
            $table->string('reference_document')->nullable(); // Ej: Orden de trabajo, ticket
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para performance
            $table->index('exit_date');
            $table->index('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_exits');
    }
};
