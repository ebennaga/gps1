<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'title' => 'Admin',
            ],
            [
                'title' => 'Manager',
            ],
            [
                'title' => 'User',
            ],
            [
                'title' => 'Distributor',
            ],
        ];

        Role::insert($roles);
    }
}
