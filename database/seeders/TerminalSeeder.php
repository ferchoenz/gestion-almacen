<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Terminal; // <-- Importante

class TerminalSeeder extends Seeder
{
    public function run(): void
    {
        // Usamos firstOrCreate para evitar duplicados
        Terminal::firstOrCreate(['name' => 'TRP']);
        Terminal::firstOrCreate(['name' => 'TRVM']);
    }
}
