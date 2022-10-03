<?php

declare(strict_types=1);

namespace Src\Utils\Foundation\Container;

use Illuminate\Contracts\Container\Container as Container;

abstract class BaseRegisterBindings
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Makes efectively the bindings
     *
     * @return void
     */
    final public function register(): void
    {
        foreach ($this->binds() as $key => $value) {
            $this->container->bind($key, $value);
        }

        foreach ($this->singletons() as $key => $value) {
            $this->container->singleton($key, $value);
        }
    }

    /**
     * Register with bind method
     *
     * @return array
     */
    protected function binds(): array
    {
        // Sobreescribir este método
        return [];
    }

    /**
     * Register singletons
     *
     * @return array
     */
    protected function singletons(): array
    {
        // Sobreescribir este método
        return [];
    }
}
