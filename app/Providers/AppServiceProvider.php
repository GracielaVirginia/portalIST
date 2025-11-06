<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Blade::directive('safeVite', function ($expression) {
            return "<?php
            try {
                echo app(Illuminate\\Contracts\\View\\Factory::class)->make('vendor.laravel.vite', ['entrypoints' => $expression])->render();
            } catch (\\Illuminate\\Foundation\\ViteManifestNotFoundException \$e) {
                echo '<!-- Vite assets not built -->';
            }
        ?>";
        });
    }
    
}
