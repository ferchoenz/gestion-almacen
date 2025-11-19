<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hazmat_products', function (Blueprint $table) {
            $table->id();
            
            // Datos Generales
            $table->string('product_name'); // Nombre Comercial
            $table->string('chemical_name'); // Nombre Químico
            $table->string('cas_number')->nullable(); // No. CAS
            
            // Datos Operativos
            $table->string('location'); // Almacen Hazmat, Taller, etc.
            $table->string('physical_state'); // Líquido, Sólido, Gas
            $table->decimal('max_quantity', 10, 2); // Cantidad Máxima
            $table->string('department'); // Dpto que lo usa
            
            // DATOS NOM-018-STPS-2015 (Lo que la IA va a llenar)
            $table->enum('signal_word', ['PELIGRO', 'ATENCION', 'SIN PALABRA']); // Palabra de Advertencia
            $table->text('hazard_statements')->nullable(); // Códigos H (Frases de Peligro)
            $table->text('precautionary_statements')->nullable(); // Códigos P (Consejos de Prudencia)
            
            // Pictogramas (Guardaremos un JSON: ["flame", "skull", "health"])
            $table->json('pictograms')->nullable();
            
            // Archivos
            $table->string('hds_path')->nullable(); // Ruta al PDF de la HDS
            $table->string('image_path')->nullable(); // Foto del producto
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes(); // Por si acaso
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hazmat_products');
    }
};