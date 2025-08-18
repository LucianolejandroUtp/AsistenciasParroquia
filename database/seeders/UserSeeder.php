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
        $profesorType = DB::table('user_types')->where('name', 'Catequista')->first();
        $staffType = DB::table('user_types')->where('name', 'Apoyo')->first();

        $users = [
            [
                'user_type_id' => $adminType->id,
                'name' => 'Administrador Principal',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin123'),
            ],
            [
                'user_type_id' => $adminType->id,
                'name' => 'Silvia Caira',
                'email' => 'silvia.caira@pcbeata.com',
                'password' => Hash::make('12345678'),
            ],
            [
                'user_type_id' => $staffType->id,
                'name' => 'Apoyo01',
                'email' => 'apoyo01@apoyo.com',
                'password' => Hash::make('apoyo123'),
            ],
            [
                'user_type_id' => $staffType->id,
                'name' => 'Apoyo02',
                'email' => 'apoyo02@apoyo.com',
                'password' => Hash::make('apoyo123'),
            ],
            [
                'user_type_id' => $profesorType->id,
                'name' => 'Angelica Caira',
                'email' => 'angelica.caira@pcbeata.com',
                'password' => Hash::make('12345678'),
            ],
            [
                'user_type_id' => $profesorType->id,
                'name' => 'Sandra Valdivia',
                'email' => 'sandra.valdivia@pcbeata.com',
                'password' => Hash::make('12345678'),
            ],
            [
                'user_type_id' => $profesorType->id,
                'name' => 'Fabiola Valdivia',
                'email' => 'fabiola.valdivia@pcbeata.com',
                'password' => Hash::make('12345678'),
            ],
            [
                'user_type_id' => $profesorType->id,
                'name' => 'Flavia Valdivia',
                'email' => 'flavia.valdivia@pcbeata.com',
                'password' => Hash::make('12345678'),
            ],
            [
                'user_type_id' => $profesorType->id,
                'name' => 'Jhean Pierr Torres',
                'email' => 'jhean.torres@pcbeata.com',
                'password' => Hash::make('12345678'),
            ]
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}
