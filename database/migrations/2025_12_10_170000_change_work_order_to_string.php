<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_receptions', function (Blueprint $table) {
            // Agregar columna para Orden de Trabajo como texto
            $table->string('work_order')->nullable()->after('purchase_order');
            
            // Eliminar la columna de path si existe (ya que no será archivo)
            if (Schema::hasColumn('material_receptions', 'work_order_path')) {
                $table->dropColumn('work_order_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('material_receptions', function (Blueprint $table) {
            $table->dropColumn('work_order');
            $table->string('work_order_path')->nullable();
        });
    }
};
