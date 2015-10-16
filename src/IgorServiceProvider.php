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
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/Http/routes.php';

        $this->publishes([
            __DIR__.'/../config/igor.php' => config_path('igor.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('/migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/stubs/Post.php' => app_path('Post.php'),
        ], 'models');

        $this->commands([
            'Jeremytubbs\Igor\Console\Commands\IgorWatchCommand',
            'Jeremytubbs\Igor\Console\Commands\IgorBuildCommand',
            'Jeremytubbs\Igor\Console\Commands\IgorNewCommand',

        ]);
    }

}