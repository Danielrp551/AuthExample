<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(UniversidadSeeder::class);
        $this->call(UsuariosSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(AssignRolesAndPermissionsSeeder::class);
    }
}
