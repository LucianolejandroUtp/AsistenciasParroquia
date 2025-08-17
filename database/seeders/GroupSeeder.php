<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'code' => 'A',
                'name' => 'Grupo A',
                'description' => 'Primer grupo de Primera Comunión 2025'
            ],
            [
                'code' => 'B', 
                'name' => 'Grupo B',
                'description' => 'Segundo grupo de Primera Comunión 2025'
            ]
        ];

        foreach ($groups as $group) {
            DB::table('groups')->insert($group);
        }
    }
}
