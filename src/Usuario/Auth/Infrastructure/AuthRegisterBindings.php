<?php

declare(strict_types=1);

namespace Src\Usuario\Auth\Infrastructure;

use Src\Usuario\Auth\Domain\LoginInterface;
use Src\Utils\Foundation\Container\BaseRegisterBindings;

class AuthRegisterBindings extends BaseRegisterBindings
{
    /**
     * Register singletons
     *
     * @return array
     */
    protected function singletons(): array
    {
        return [LoginInterface::class => LoginService::class];
    }
}
