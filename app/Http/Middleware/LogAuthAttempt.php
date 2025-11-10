<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\LoginAttempt;
use App\Models\Paciente;

class LogAuthAttempt
{
    public function handle(Request $request, Closure $next)
    {
        // === 1) VISITA A LOGIN ===
        if ($this->isLoginPageVisit($request)) {
            // intenta capturar el documento desde el request o desde el usuario autenticado (si lo hay)
            $loginInput = $this->extractLoginInputFromRequest($request);

            if (!$loginInput && ($u = $request->user())) {
                // si ya está autenticado y tu User tiene rut/pasaporte/email, úsalo
                $loginInput = $u->rut ?? $u->pasaporte ?? $u->email ?? null;
            }

            $this->storeAttempt([
                'user'        => $request->user(), // puede ser null
                'login_input' => $loginInput ? trim(strtoupper($loginInput)) : '',
                'outcome'     => 'visit',
                'ip'          => $request->ip(),
                'ua'          => $request->userAgent(),
            ]);
        }

        // === 2) VERIFICACIÓN DE PACIENTE (rut/pasaporte) ===
        if ($this->isVerificationRoute($request)) {
            $input  = $this->extractLoginInputFromRequest($request);
            $exists = $this->patientExists($request, $input);

            $this->storeAttempt([
                'login_input' => $input,
                'outcome'     => $exists ? 'verify_found' : 'verify_not_found',
                'ip'          => $request->ip(),
                'ua'          => $request->userAgent(),
            ]);
        }

        // === 3) PASA AL CONTROLADOR ===
        $response = $next($request);

        // === 4) INTENTO DE LOGIN ===
        if ($this->isLoginAttemptRoute($request)) {
            $user   = $request->user();
            $input  = $this->extractLoginInputFromRequest($request)
                ?: ($user->rut ?? $user->pasaporte ?? $user->email ?? '');
            $errors = session('errors')?->getBag('default')?->all() ?? [];

            $joined    = Str::lower(implode(' ', $errors));
            $outcome   = 'user_not_found';
            $isBlocked = false;

            if ($user) {
                // Se autenticó correctamente (credenciales válidas).
                // Aún no implica que llegó al Home: eso se registra como 'portal_access'.
                $outcome = 'login_success';
            } else {
                if (Str::contains($joined, ['throttle', 'demasiados', 'too many'])) {
                    $outcome   = 'blocked';
                    $isBlocked = true;
                } elseif (Str::contains($joined, ['contraseña', 'password'])) {
                    $outcome = 'invalid_password';
                }
            }

            $this->storeAttempt([
                'user'        => $user,
                'login_input' => $input,
                'outcome'     => $outcome,
                'is_blocked'  => $isBlocked,
                'blocked_at'  => $isBlocked ? now() : null,
                'ip'          => $request->ip(),
                'ua'          => $request->userAgent(),
            ]);
        }

        // === 5) VALIDACIÓN POST-LOGIN (2FA / estado / activo) ===
        if ($this->isValidationRoute($request)) {
            $user  = $request->user();
            $input = $this->extractLoginInputFromRequest($request)
                ?: ($user->rut ?? $user->pasaporte ?? $user->email ?? '');

            // Si hay sesión iniciada pero falla una validación interna
            $validationPassed = $this->checkInternalValidation($request);

            if ($validationPassed) {
                // OJO: Esto NO es "éxito final". Solo indica que su validación interna pasó.
                // El verdadero éxito es 'portal_access' y se registra al entrar a portal.home.
                $this->storeAttempt([
                    'user'        => $user,
                    'login_input' => $input,
                    'outcome'     => 'validation_passed',
                    'ip'          => $request->ip(),
                    'ua'          => $request->userAgent(),
                ]);
            } else {
                $blocked = $this->isValidationBlocked($request);

                $this->storeAttempt([
                    'user'        => $user,
                    'login_input' => $input,
                    'outcome'     => $blocked ? 'validation_blocked' : 'validation_failed',
                    'is_blocked'  => $blocked,
                    'blocked_at'  => $blocked ? now() : null,
                    'ip'          => $request->ip(),
                    'ua'          => $request->userAgent(),
                ]);
            }
        }

        // === 6) ÉXITO REAL: ACCESO AL HOME DEL PORTAL ===
        // Se registra DESPUÉS de pasar por el controlador para no interferir con la navegación.
        if ($this->isPortalHomeRoute($request)) {
            $user  = $request->user();
            $input = $this->extractLoginInputFromRequest($request)
                ?: ($user->rut ?? $user->pasaporte ?? $user->email ?? '');

            $this->storeAttempt([
                'user'        => $user,
                'login_input' => $input,
                'outcome'     => 'portal_access', // Éxito final
                'ip'          => $request->ip(),
                'ua'          => $request->userAgent(),
            ]);
        }

        return $response;
    }

    /* ==== HELPERS DE RUTAS ==== */

    protected function isLoginPageVisit(Request $r): bool
    {
        return $r->isMethod('GET') && $r->routeIs('login');
    }

    protected function isVerificationRoute(Request $r): bool
    {
        return $r->routeIs('verificar-rut', 'verificar-pasaporte') && $r->isMethod('POST');
    }

    protected function isLoginAttemptRoute(Request $r): bool
    {
        return $r->routeIs('login.attempt', 'login.attempt.pasaporte') && $r->isMethod('POST');
    }

    protected function isValidationRoute(Request $r): bool
    {
        // Solo rutas reales de validación (SIN portal.home)
        return $r->routeIs('validacion.*', 'auth.validacion') && $r->isMethod('GET');
    }

    protected function isPortalHomeRoute(Request $r): bool
    {
        return $r->routeIs('portal.home') && $r->isMethod('GET');
    }

    /* ==== HELPERS DE DOMINIO ==== */

    protected function checkInternalValidation(Request $r): bool
    {
        // Define tu lógica real de validación interna (activo, flags, 2FA, etc.)
        $user = $r->user();
        if (!$user) return false;

        // Ejemplo: si el modelo tiene 'valido' o 'activo'
        return property_exists($user, 'valido') ? (bool)$user->valido : true;
    }

    protected function isValidationBlocked(Request $r): bool
    {
        // Detecta bloqueos en validación por mensajes de error en sesión
        $msg = Str::lower(implode(' ', session('errors')?->getBag('default')?->all() ?? []));
        return Str::contains($msg, ['bloqueado', 'demasiados', 'too many']);
    }

    protected function patientExists(Request $r, string $input): bool
    {
        if (!$input) return false;

        try {
            $isRut = $r->has('rut') || $this->looksLikeRut($input);

            return $isRut
                ? Paciente::where('rut', $input)->exists()
                : Paciente::where('pasaporte', $input)->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    protected function looksLikeRut(string $value): bool
    {
        return (bool) preg_match('/^\d{6,9}-?[\dkK]$/', $value);
    }

    protected function extractLoginInputFromRequest(Request $r): string
    {
        foreach (['rut', 'pasaporte', 'documento', 'email', 'username', 'login', 'user'] as $k) {
            $v = $r->input($k);
            if (!empty($v) && !is_array($v)) {
                return trim((string) $v);
            }
        }
        return '';
    }

    protected function storeAttempt(array $data): void
    {
        $ip = $data['ip'] ?? '0.0.0.0';
        $ua = $data['ua'] ?? '';
        $loginInput = $data['login_input'] ?? '';

        // Conteo del día (por IP + login_input si existe)
        $count = LoginAttempt::where('ip_address', $ip)
            ->when(!empty($loginInput), fn ($q) => $q->where('login_input', $loginInput))
            ->whereDate('created_at', today())
            ->count() + 1;

        LoginAttempt::create([
            'user_id'        => optional($data['user'] ?? null)->id,
            'login_input'    => $loginInput,
            'ip_address'     => $ip,
            'user_agent'     => Str::limit($ua, 255, ''),
            'outcome'        => $data['outcome'] ?? 'unknown',
            'attempt_number' => $count,
            'is_blocked'     => $data['is_blocked'] ?? false,
            'blocked_at'     => $data['blocked_at'] ?? null,
        ]);
    }
}
