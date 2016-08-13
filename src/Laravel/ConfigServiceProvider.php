<?php

namespace Inoplate\Config\Laravel;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Inoplate\Config\Config',
            'Inoplate\Config\DatabaseConfig');

        $this->app->bind('Inoplate\Config\Repositories\Config', 
            'Inoplate\Config\Laravel\EloquentConfig');
    }
}
