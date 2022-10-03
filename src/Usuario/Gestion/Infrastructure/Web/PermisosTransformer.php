<?php

declare(strict_types=1);

namespace Src\Usuario\Gestion\Infrastructure\Web;

use Illuminate\Database\Eloquent\Collection;

/**
 * Lee los permisos de la request y los formatea a como los trabaja la librería Spattie
 */
class PermisosTransformer
{
    public static function fromRequest(array $request)
    {
        $permisos = [];
        if (isset($request['permisos'])) {
            foreach (array_keys($request['permisos'], true) as $permiso) {
                $permisos[] = ['name' => $permiso];
            }
        }
        return $permisos;
    }

    public static function fromSpatie(Collection $permisos): array
    {
        return $permisos->pluck('name')->toArray();
    }
}
