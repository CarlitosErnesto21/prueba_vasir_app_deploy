<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);
    }*/
    public function run()
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);
    }
}
