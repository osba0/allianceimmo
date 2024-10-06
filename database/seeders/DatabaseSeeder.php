<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionGroupsTableSeeder::class,
            RolesAndPermissionsSeeder::class,
            UsersTableSeeder::class,
            AgenceSeeder::class,
            //PersonnelsTableSeeder::class, gerer dans UsersTableSeeder
        ]);
    }
}
