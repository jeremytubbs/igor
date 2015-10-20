<?php

namespace Jeremytubbs\Igor;

use Illuminate\Support\ServiceProvider;

class IgorServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/Http/routes.php';
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'lair');
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
            __DIR__.'/../database/migrations/' => database_path('/migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/stubs/Post.php' => app_path('Post.php'),
        ], 'models');

        $this->publishes([
            __DIR__.'/../static/' => base_path('resources/static'),
        ], 'static');

        $this->commands([
            'Jeremytubbs\Igor\Console\Commands\IgorWatchCommand',
            'Jeremytubbs\Igor\Console\Commands\IgorBuildCommand',
            'Jeremytubbs\Igor\Console\Commands\IgorNewCommand',

        ]);
    }

}