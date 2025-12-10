<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL: Necesitamos eliminar el constraint y agregar uno nuevo
            DB::statement("ALTER TABLE material_receptions DROP CONSTRAINT IF EXISTS material_receptions_status_check");
            
            // Crear nuevo constraint con PENDIENTE_OT
            DB::statement("ALTER TABLE material_receptions ADD CONSTRAINT material_receptions_status_check CHECK (status::text = ANY (ARRAY['PENDIENTE_UBICACION'::text, 'PENDIENTE_OT'::text, 'COMPLETO'::text]))");
        } elseif ($driver === 'mysql') {
            // MySQL: Modificar la columna ENUM para incluir el nuevo valor
            DB::statement("ALTER TABLE material_receptions MODIFY COLUMN status ENUM('PENDIENTE_UBICACION', 'COMPLETO', 'PENDIENTE_OT') NOT NULL DEFAULT 'PENDIENTE_UBICACION'");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE material_receptions DROP CONSTRAINT IF EXISTS material_receptions_status_check");
            DB::statement("ALTER TABLE material_receptions ADD CONSTRAINT material_receptions_status_check CHECK (status::text = ANY (ARRAY['PENDIENTE_UBICACION'::text, 'COMPLETO'::text]))");
        } elseif ($driver === 'mysql') {
            // Revertir a los valores originales
            DB::statement("ALTER TABLE material_receptions MODIFY COLUMN status ENUM('PENDIENTE_UBICACION', 'COMPLETO') NOT NULL DEFAULT 'PENDIENTE_UBICACION'");
        }
    }
};
