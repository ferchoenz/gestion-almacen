<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role; // <-- Importante: Usamos el modelo Role

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de roles definidos en tus requisitos
        $roles = [
            'Administrador',
            'Seguridad y Salud',
            'Mantenimiento',
            'Gerencia'
        ];

        foreach ($roles as $roleName) {
            // firstOrCreate: Busca si existe por 'name'.
            // Si existe, no hace nada. Si no existe, lo crea.
            // Esto evita el error de "Duplicate entry" o "Unique violation".
            Role::firstOrCreate(['name' => $roleName]);
        }
    }
}