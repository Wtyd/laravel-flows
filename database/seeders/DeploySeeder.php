<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Propósito: instalar la aplicación. Se ejecuta una única vez.
 * Datos: los mínimos imprescindibles (datos de configuración)
 */
class DeploySeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ManageRolesAndUsersPermissionSeeder::class,
            AdminUserSeeder::class
        ]);
    }
}
