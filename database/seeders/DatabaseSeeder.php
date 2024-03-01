<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            BuffetSeeder::class,
            UserSeeder::class,
            FoodSeeder::class, 

            // TestsSeeder::class
        ]);
    }
}
