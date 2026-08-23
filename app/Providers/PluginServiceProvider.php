<?php

namespace App\Providers;

use App\Services\Plugin\PluginManager;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('barta.plugin', fn () => new PluginManager());
        $this->app->alias('barta.plugin', PluginManager::class);
    }

    public function boot(): void
    {
        // Load active plugins: each registers its own service provider, which
        // may add hooks, routes, views, migrations, etc.
        $this->app->make('barta.plugin')->boot();
    }
}
