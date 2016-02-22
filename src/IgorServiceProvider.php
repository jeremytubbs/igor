<?php

namespace Jeremytubbs\Igor;

use Illuminate\Support\ServiceProvider;
use Jeremytubbs\Igor\Repositories\IgorEloquentRepository as IgorRepository;

class IgorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->setEventListeners();

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

        $this->publishes([
            __DIR__.'/../config/igor.php' => config_path('igor.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/stubs/Models/Asset.php' => app_path('Asset.php'),
            __DIR__.'/stubs/Models/Tag.php' => app_path('Tag.php'),
            __DIR__.'/stubs/Models/Category.php' => app_path('Category.php'),
            __DIR__.'/stubs/Models/Content.php' => app_path('Content.php'),
        ], 'models');

        $this->loadViewsFrom(__DIR__.'/views', 'igor');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            'Jeremytubbs\Igor\Console\Commands\IgorWatchCommand',
            'Jeremytubbs\Igor\Console\Commands\IgorBuildCommand',
            'Jeremytubbs\Igor\Console\Commands\IgorNewCommand',
        ]);

        $this->app->bind('Jeremytubbs\Igor\Contracts\IgorRepositoryInterface',
            'Jeremytubbs\Igor\Repositories\IgorEloquentRepository');

        $this->app->register('Jeremytubbs\LaravelResizer\ResizerServiceProvider');
        $this->app->register('Jeremytubbs\LaravelDeepzoom\DeepzoomServiceProvider');
        $this->app->register('Roumen\Sitemap\SitemapServiceProvider');
    }

    public function setEventListeners()
    {
        \Event::listen('resizer', function($data) {
            (new IgorRepository)->updatePostAssets($data);
        });
        \Event::listen('deepzoom', function($data) {
            (new IgorRepository)->updatePostAssets($data);
        });
    }
}