<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden de dependencias
        $this->call([
            UserTypeSeeder::class,  // Primero: tipos de usuario
            UserSeeder::class,      // Segundo: usuarios (depende de user_types)
            GroupSeeder::class,     // Tercero: grupos (independiente)
            StudentSeeder::class,   // Cuarto: estudiantes (depende de groups)
        ]);
    }
}
