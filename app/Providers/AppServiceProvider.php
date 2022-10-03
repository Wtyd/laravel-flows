<?php

namespace App\Providers;

use App\Http\Kernel;
use Faker\Generator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Utils\CustomFaker\CustomFakerProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Kernel $kernel)
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // TODO consultar con José por Telescope
        if (! $this->app->environment('production', 'producción', 'produccion')) {
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->registerCollectionPaginator();

        if ($this->isStageEnvironment()) {
            $this->app->singleton(Generator::class, function () {
                $faker = \Faker\Factory::create(config('app.faker_locale'));

                return CustomFakerProvider::providersRegister($faker);
            });
            $this->app->alias(Generator::class, 'Faker');
        }
    }

    private function registerCollectionPaginator()
    {
        // Enable pagination
        if (!Collection::hasMacro('paginate')) {
            Collection::macro(
                'paginate',
                function ($perPage = 15, $page = null, $options = []) {
                    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
                    return (new LengthAwarePaginator(
                        $this->forPage($page, $perPage)->values()->all(),
                        $this->count(),
                        $perPage,
                        $page,
                        $options
                    ))
                    ->withPath('');
                }
            );
        }
    }

    /**
     * Entornos de pruebas
     *
     * @return boolean
     */
    protected function isStageEnvironment(): bool
    {
        return in_array($this->app->environment(), config('app.stage_environments'));
    }
}
