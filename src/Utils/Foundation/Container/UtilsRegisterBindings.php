<?php

declare(strict_types=1);

namespace Src\Utils\Foundation\Container;

/**
 * Registro de Interfaces en el Contenedor para el módulo Utils
 */
class UtilsRegisterBindings extends BaseRegisterBindings
{
    /**
     * Register with bind method
     *
     * @return array
     */
    protected function binds(): array
    {
        return [];
    }

    /**
     * Register singletons
     *
     * @return array
     */
    protected function singletons(): array
    {
        return [];
    }
}
