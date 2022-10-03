<?php

namespace Database\Seeders\Modulos;

use Src\Usuario\Shared\Infrastructure\Persistence\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Src\Usuario\Gestion\Infrastructure\Persistence\Seeders\AdminUserSeeder;
use Src\Usuario\Gestion\Infrastructure\Persistence\Seeders\ManageRolesAndUsersPermissionSeeder;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Getting existing roles
        $this->call(
            ManageRolesAndUsersPermissionSeeder::class,
            AdminUserSeeder::class
        );

        //Getting existing roles
        $roles = DB::table('roles')->get();

        User::factory(10)   //->count(10) funciona igual
            ->create()
            ->each(function ($user) use ($roles) {
                $user->save();

                $user->assignRole($roles->random()->name);
            });
    }
}
