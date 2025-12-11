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
        Schema::create('hazmat_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Solicitante
            $table->foreignId('terminal_id')->constrained()->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users'); // Quién aprobó/rechazó
            
            // Estado del flujo
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->text('rejection_reason')->nullable();
            
            // Datos del Material (Sección 1 y 2 del Formato)
            $table->date('entry_date')->nullable();
            $table->string('trade_name');     // Nombre Comercial / Fabricante
            $table->string('chemical_name');  // Nombre Químico
            $table->string('usage_area');     // Área en la que se utiliza
            $table->text('intended_use');     // Descripción del uso previsto
            $table->string('storage_location'); // Lugar(es) de almacenamiento (propuesto por usuario)
            $table->string('max_storage_quantity');
            $table->string('min_storage_quantity')->nullable();
            $table->string('monthly_consumption')->nullable();
            
            $table->boolean('is_sample')->default(false);
            $table->boolean('is_import')->default(false);
            $table->string('moc_id')->nullable(); // MOC
            
            $table->string('hds_path')->nullable(); // Archivo HDS subido
            
            // Sección de Validación (Llenada por Seguridad/Champion)
            $table->boolean('can_be_substituted')->nullable();
            $table->boolean('hds_compliant')->nullable();
            $table->boolean('has_training')->nullable();
            $table->boolean('has_ppe')->nullable();
            $table->boolean('has_containment')->nullable();
            $table->boolean('moc_managed')->nullable();
            
            $table->string('final_storage_location')->nullable(); // ¿En dónde se debe almacenar...?
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hazmat_requests');
    }
};
