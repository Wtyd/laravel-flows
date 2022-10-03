<?php

namespace Database\Seeders;

use Database\Seeders\Modulos\UsuariosSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Propósito: pruebas manuales.
 * Datos: conjunto completo de datos con todas o casi todas las variantes
 */
class ManualTestingSeeder extends Seeder
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
            UsuariosSeeder::class,
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
