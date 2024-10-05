<?php

namespace rateb\structure;


use Illuminate\Support\ServiceProvider;
use rateb\structure\Console\Commands\MakeDTO;
use rateb\structure\Console\Commands\MakeFilter;
use rateb\structure\Console\Commands\MakeRepositoryAndDTO;


class StructureServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * This method is for binding services, repositories, and other components into the service container.
     */
    public function register()
    {
        // Register your command here
        $this->commands([
            MakeRepositoryAndDTO::class,
            MakeDTO::class,
            MakeFilter::class,


        ]);
    }

    /**
     * Bootstrap any application services.
     * This method is for bootstrapping things like publishing configurations, routes, etc.
     */
    public function boot()
    {
        if (file_exists(__DIR__.'/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        // Publishing stub files
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/stubs' => base_path('stubs/vendor/rateb/structure'),
            ], 'structure-stubs');
        }

    }
}
