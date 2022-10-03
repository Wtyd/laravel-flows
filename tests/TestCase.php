<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Zataca\Hydrator\HydratorTrait;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;
    use CreatesApplication;
    use HydratorTrait;

    public function getTodosLosPermisos(): Collection
    {
        return DB::table('permissions')->get()->pluck('name');
    }

    /**
     * Crea los permisos en la bdd. Los permisos deben existir antes de ser asignados a un usuario.
     *
     * @param array $permisos Cada elemento del array es un string con el nombre del permiso
     * @return void
     */
    public function crearPermisos(array $permisos): void
    {
        foreach ($permisos as $permiso) {
            Permission::findOrCreate($permiso);
        }
    }
}
