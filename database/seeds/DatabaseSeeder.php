<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {       
            $this->call(\Database\Seeds\PermissionsTableSeeder::class);
            $this->call(\Database\Seeds\RolesTableSeeder::class);
            $this->call(\Database\Seeds\UsersTableSeeder::class);
            $this->call(\Database\Seeds\ConnectRelationshipsSeeder::class);

    }
}