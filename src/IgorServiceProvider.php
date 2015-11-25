<?php

namespace Jeremytubbs\Igor;

use Illuminate\Support\ServiceProvider;

class IgorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/Http/routes.php';
        }

        // Publish the migration
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('/migrations'),
        ], 'migrations');

        // Publish static directory with config.yaml
        $this->publishes([
            __DIR__.'/../static/' => base_path('resources/static'),
        ], 'static');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__.'/../config/igor.php' => config_path('igor.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/stubs/Models/Tag.php' => app_path('Tag.php'),
            __DIR__.'/stubs/Models/Category.php' => app_path('Category.php'),
        ], 'models');

        $this->commands([
            'Jeremytubbs\Igor\Console\Commands\IgorWatchCommand',
            'Jeremytubbs\Igor\Console\Commands\IgorBuildCommand',
            'Jeremytubbs\Igor\Console\Commands\IgorNewCommand',
        ]);
    }

}