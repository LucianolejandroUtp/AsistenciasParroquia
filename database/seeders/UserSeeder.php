<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs de user_types
        $adminType = DB::table('user_types')->where('name', 'Admin')->first();
        $profesorType = DB::table('user_types')->where('name', 'Profesor')->first();
        $staffType = DB::table('user_types')->where('name', 'Staff')->first();

        $users = [
            [
                'user_type_id' => $adminType->id,
                'name' => 'Administrador Principal',
                'email' => 'admin@primcomunion.edu',
                'password' => Hash::make('admin123'),
            ],
            [
                'user_type_id' => $profesorType->id,
                'name' => 'Prof. María González',
                'email' => 'maria.gonzalez@primcomunion.edu',
                'password' => Hash::make('prof123'),
            ],
            [
                'user_type_id' => $profesorType->id,
                'name' => 'Prof. Juan Pérez',
                'email' => 'juan.perez@primcomunion.edu', 
                'password' => Hash::make('prof123'),
            ],
            [
                'user_type_id' => $staffType->id,
                'name' => 'Ana Rodríguez',
                'email' => 'ana.rodriguez@primcomunion.edu',
                'password' => Hash::make('staff123'),
            ]
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}
