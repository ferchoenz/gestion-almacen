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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumable_id')->constrained()->onDelete('cascade');
            $table->foreignId('terminal_id')->constrained()->onDelete('cascade');
            
            // Movement type
            $table->enum('movement_type', [
                'ENTRADA',              // Recepción/Compra
                'SALIDA',               // Consumo/Entrega
                'AJUSTE_POSITIVO',      // Inventario físico (aumento)
                'AJUSTE_NEGATIVO',      // Inventario físico (reducción)
                'TRANSFERENCIA_SALIDA', // Transferencia a otra ubicación
                'TRANSFERENCIA_ENTRADA' // Recepción de transferencia
            ]);
            
            // Quantities
            $table->decimal('quantity', 10, 2); // Cantidad del movimiento
            $table->decimal('previous_stock', 10, 2); // Stock anterior
            $table->decimal('new_stock', 10, 2); // Stock después del movimiento
            
            // Unit cost at time of movement
            $table->decimal('unit_cost', 10, 2)->nullable();
            
            // Reference to source document (polymorphic)
            $table->string('reference_type')->nullable(); // App\Models\Reception, App\Models\InventoryExit
            $table->unsignedBigInteger('reference_id')->nullable();
            
            // Additional info
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Quién hizo el movimiento
            
            $table->timestamps();
            
            // Indexes for Kardex queries
            $table->index(['consumable_id', 'created_at']);
            $table->index(['movement_type']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
