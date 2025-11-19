<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llama a los seeders que acabamos de crear
        $this->call([
            TerminalSeeder::class,
            RoleSeeder::class,
        ]);

        // \App\Models\User::factory(10)->create(); // Comenta o borra esto
    }
}
