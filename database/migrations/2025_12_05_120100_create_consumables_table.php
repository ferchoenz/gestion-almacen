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
        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terminal_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique(); // Código único del producto
            $table->string('name'); // Nombre del producto
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // Categoría (Eléctrico, Plomería, etc.)
            $table->string('unit_of_measure')->default('PZA'); // PZA, KG, LT, M, etc.
            
            // Stock control
            $table->decimal('current_stock', 10, 2)->default(0);
            $table->decimal('min_stock', 10, 2)->default(0); // Punto de reorden
            $table->decimal('max_stock', 10, 2)->nullable(); // Stock máximo recomendado
            
            // Pricing
            $table->decimal('unit_cost', 10, 2)->nullable(); // Costo unitario promedio
            
            // Location
            $table->foreignId('location_id')->nullable()->constrained('inventory_locations')->nullOnDelete();
            
            // Media
            $table->string('image_path')->nullable();
            $table->string('barcode')->nullable()->unique(); // Código de barras generado
            
            // Status
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['terminal_id', 'sku']);
            $table->index(['category']);
            $table->index('current_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumables');
    }
};
