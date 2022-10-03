<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Src\Usuario\Auth\Infrastructure\AuthRegisterBindings;
use Src\Usuario\Gestion\Infrastructure\UsuarioRegisterBindings;
use Src\Utils\Foundation\Container\UtilsRegisterBindings;

class RegisterBindingsServiceProvider extends ServiceProvider
{
    /**
     * Seguir orden alfabético para facilitar encontrar los módulos
     *
     * @var array
     */
    protected $packagesBindingsRegister = [
        AuthRegisterBindings::class,
        UsuarioRegisterBindings::class,
        UtilsRegisterBindings::class,
    ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->packagesBindingsRegister as $packageRegister) {
            $register = new $packageRegister($this->app);
            $register->register();
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
