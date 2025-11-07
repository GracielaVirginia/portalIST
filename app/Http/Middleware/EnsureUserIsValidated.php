<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsValidated
{
    // Rutas que pueden pasar aunque no esté validado (para no romper login/assets).
    protected array $whitelist = [
        'login', 'logout', 'password/*',
        'up',
        'assets/*', 'build/*', 'storage/*',
        // Si tus pantallas de validación son públicas, puedes dejarlas aquí también:
        'validacion/*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Sin sesión: que pase; lo controla 'auth' en otra capa.
        if (! $request->user()) {
            return $next($request);
        }

        // Si está validado, OK.
        if ($request->user()->is_validated) {
            return $next($request);
        }

        // Permitir solo rutas whitelisted (login, assets, etc.)
        foreach ($this->whitelist as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        // No validado intentando entrar al portal -> cerrar sesión y enviar al login
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // AJAX/JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Tu sesión fue cerrada. Debes validar tu cuenta para acceder.',
                'code'    => 'ACCOUNT_NOT_VALIDATED',
            ], 401);
        }

        // Redirección al login con mensaje
        return redirect()->route('login')
            ->with('warning', 'No puedes acceder a esta página sin validar tu cuenta. Inicia sesión y completa la validación.');
    }
}
