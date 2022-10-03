<?php

namespace Src\Usuario\Gestion\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ManageRolesAndUsersPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::firstOrCreate(['name' => 'administrador']);
        Role::firstOrCreate(['name' => 'user']);

        Permission::findOrCreate('manage_users');
        Permission::findOrCreate('ver_usuario');
        Permission::findOrCreate('editar_usuario');
        // Sync will delete then re-add all the permissions.
        $admin->syncPermissions([
            'manage_users',
        ]);
    }
}
