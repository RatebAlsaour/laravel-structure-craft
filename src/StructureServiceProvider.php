<?php

namespace RatebSa\Structure;


use Illuminate\Support\ServiceProvider;
use RatebSa\Structure\Console\Commands\MakeDTO;
use RatebSa\Structure\Console\Commands\MakeFilter;
use RatebSa\Structure\Console\Commands\MakeRepositoryAndDTO;


class StructureServiceProvider extends ServiceProvider
{
    /**
     * Register any application services. v2.0
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
