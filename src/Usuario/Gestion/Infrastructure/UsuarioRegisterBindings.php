<?php

declare(strict_types=1);

namespace Src\Usuario\Gestion\Infrastructure;

use Src\Usuario\Gestion\Domain\UsuarioRepositoryInterface;
use Src\Usuario\Gestion\Infrastructure\Persistence\UsuarioRepository;
use Src\Utils\Foundation\Container\BaseRegisterBindings;

class UsuarioRegisterBindings extends BaseRegisterBindings
{
    /**
     * Register singletons
     *
     * @return array
     */
    protected function singletons(): array
    {
        return [UsuarioRepositoryInterface::class => UsuarioRepository::class];
    }
}
