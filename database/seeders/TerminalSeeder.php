<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TerminalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta tus dos terminales
        DB::table('terminals')->insert([
            ['name' => 'TRP'],
            ['name' => 'TRVM'],
        ]);
    }
}
