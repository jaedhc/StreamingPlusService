<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserRoles;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        UserRoles::create([
            'type' => 'Admin'
        ]);

        UserRoles::create([
            'type' => 'User'
        ]);
    }
}
