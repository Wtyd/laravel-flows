<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */


    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        Route::prefix('api/usuarios')
            ->middleware(['api', 'auth:sanctum', 'throttle: 60,1'])
            ->group(base_path('src/Usuario/Gestion/Infrastructure/routes.php'));

        Route::prefix('api')
            ->middleware(['api', 'auth:sanctum', 'throttle: 60,1'])
            ->group(base_path('src/Usuario/Auth/Infrastructure/routes.php'));

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        if ($this->app->environment('local')) { //Esto lo añadió Enrique e Izhar en el otro método map
            $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
                ->pushMiddleware(\Fruitcake\Cors\HandleCors::class);
        }
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
