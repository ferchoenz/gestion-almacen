<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta los roles que definiste
        DB::table('roles')->insert([
            ['name' => 'Administrador'],
            ['name' => 'Seguridad y Salud'],
            ['name' => 'Mantenimiento'],
            ['name' => 'Gerencia'],
        ]);
    }
}
