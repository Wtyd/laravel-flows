<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Src\Usuario\Gestion\Infrastructure\Persistence\Seeders\{
    AdminUserSeeder,
    ManageRolesAndUsersPermissionSeeder,
};

/**
 * Propósito: pruebas automáticas.
 * Datos: los mínimos imprescindibles (datos de configuración)
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (! $this->shouldSeedsBeRun()) {
            return;
        }

        $this->command->info('Comienza a ejecutarse el DatabaseSeeder:');
        DB::beginTransaction();

        Schema::disableForeignKeyConstraints();

        $this->call([
            TruncateAllTablesSeeder::class,
            ManageRolesAndUsersPermissionSeeder::class,
            AdminUserSeeder::class
        ]);

        Schema::enableForeignKeyConstraints();
        DB::commit();
    }

    /**
     * Se puede lanzar en entornos locales y de testing. No se puede lanzar en entornos de producción.
     *
     * @return boolean
     */
    protected function shouldSeedsBeRun()
    {
        if (in_array(app()->environment(), config('app.stage_environments'))) {
            return true;
        }

        $this->command->error('El seeder no se puede lanzar en este entorno: ' . app()->environment());
        return false;
    }
}
