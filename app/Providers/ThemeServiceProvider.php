<?php

namespace App\Providers;

use App\Services\Theme\ThemeManager;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('barta.theme', fn () => new ThemeManager());
        $this->app->alias('barta.theme', ThemeManager::class);
    }

    public function boot(): void
    {
        // Register the `theme::` view namespace for the active theme so
        // controllers/components can render `view('theme::home')`.
        $this->app->make('barta.theme')->boot();
    }
}
