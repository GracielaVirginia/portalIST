<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\AlertaService;
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
          $this->app->singleton(AlertaService::class, fn() => new AlertaService());
    }

    public function boot(): void
    {
                /*
        |----------------------------------------------------------------------
        | A) Registrar Observer de User (IMPORTANTE: al inicio de boot)
        |----------------------------------------------------------------------
        | Así se ejecuta SIEMPRE, incluso si luego sales con 'return' al
        | detectar algunos comandos de consola.
        */
        User::observe(UserObserver::class);
        /*
        |--------------------------------------------------------------------------
        | 1) Directiva @safeVite (tu código original, intacto)
        |--------------------------------------------------------------------------
        */
        Blade::directive('safeVite', function ($expression) {
            return "<?php
            try {
                echo app(Illuminate\\Contracts\\View\\Factory::class)
                    ->make('vendor.laravel.vite', ['entrypoints' => $expression])
                    ->render();
            } catch (\\Illuminate\\Foundation\\ViteManifestNotFoundException \$e) {
                echo '<!-- Vite assets not built -->';
            }
        ?>";
        });

        /*
        |--------------------------------------------------------------------------
        | 2) Config dinámico desde tabla other_settings
        |--------------------------------------------------------------------------
        | Lee session_timeout y font_family desde la base de datos.
        | Evita ejecutarse durante comandos artisan que no necesitan conexión.
        */
        if ($this->app->runningInConsole()) {
            $cmd = implode(' ', $_SERVER['argv'] ?? []);
            if (Str::contains($cmd, ['migrate', 'db:', 'queue:', 'test', 'tinker'])) {
                return;
            }
        }

        try {
            // Cacheamos para no consultar la BD en cada request
            $settings = Cache::remember('other_settings', 300, function () {
                return DB::table('other_settings')->first();
            });

            if ($settings) {
                // Aplica el tiempo de inactividad dinámico
                $timeout = (int) ($settings->session_timeout ?? 20);
                if ($timeout > 0) {
                    Config::set('session.lifetime', $timeout);
                }

                // Comparte configuración visual (ej: fuente) con todas las vistas
                view()->share('appSettings', $settings);
            }
        } catch (\Throwable $e) {
            // Evita romper el arranque si la tabla no existe aún
        }
    }
}
