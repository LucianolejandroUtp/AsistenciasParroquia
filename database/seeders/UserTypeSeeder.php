<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTypes = [
            [
                'name' => 'Admin',
                'description' => 'Administrador del sistema con acceso completo'
            ],
            [
                'name' => 'Catequista',
                'description' => 'Catequista responsable de grupos de estudiantes'
            ],
            [
                'name' => 'Apoyo',
                'description' => 'Personal de apoyo'
            ]
        ];

        foreach ($userTypes as $userType) {
            DB::table('user_types')->insert($userType);
        }
    }
}
