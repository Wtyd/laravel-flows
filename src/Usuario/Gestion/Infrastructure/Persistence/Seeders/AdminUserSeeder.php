<?php

namespace Src\Usuario\Gestion\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::updateOrCreate(
            [
                'email' => 'superadmin@zataca.com',
            ],
            [
                'name' => 'SuperAdmin',
                'email' => 'superadmin@zataca.com',
                'phone_number' => '661637458',
                'password' => Hash::make(config('app.super_admin_password')),
            ]
        );

        $admin->assignRole('administrador');
    }
}
