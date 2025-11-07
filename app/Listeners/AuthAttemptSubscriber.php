<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Request;
use App\Models\LoginAttempt;
use Carbon\Carbon;

class AuthAttemptSubscriber
{
    /** Suscripción a eventos */
    public function subscribe($events): void
    {
        $events->listen(Login::class,  [self::class, 'onLogin']);
        $events->listen(Failed::class, [self::class, 'onFailed']);
        $events->listen(Lockout::class,[self::class, 'onLockout']);
    }

    /** Login exitoso */
    public function onLogin(Login $event): void
    {
        $loginInput = $this->resolveLoginInput($event->guard, $event->user);
        $this->storeAttempt([
            'user'          => $event->user,
            'login_input'   => $loginInput,
            'outcome'       => 'success',
            'is_blocked'    => false,
            'blocked_at'    => null,
        ]);
    }

    /** Credenciales fallidas: usuario no encontrado o password errada */
    public function onFailed(Failed $event): void
    {
        $credentials = (array) ($event->credentials ?? []);
        $loginInput  = $this->extractLoginInputFromCredentials($credentials);

        // Si hay $event->user => password inválida; si no hay => usuario no existe
        $outcome = $event->user instanceof Authenticatable ? 'invalid_password' : 'user_not_found';

        $this->storeAttempt([
            'user'          => $event->user, // puede ser null
            'login_input'   => $loginInput,
            'outcome'       => $outcome,
            'is_blocked'    => false,
            'blocked_at'    => null,
        ]);
    }

    /** Bloqueo por throttling (Too Many Attempts) */
    public function onLockout(Lockout $event): void
    {
        $request    = $event->request;
        $loginInput = $this->extractLoginInputFromRequest($request);

        $this->storeAttempt([
            'user'          => null, // normalmente no autenticado aún
            'login_input'   => $loginInput,
            'outcome'       => 'blocked',
            'is_blocked'    => true,
            'blocked_at'    => Carbon::now(),
        ]);
    }

    /** Persiste el intento */
    protected function storeAttempt(array $data): void
    {
        $request = Request::instance();

        // Calcula el número de intento acumulado para ese login_input (puedes limitar por día si prefieres)
        $attemptNumber = 1;
        if (!empty($data['login_input'])) {
$attemptNumber = LoginAttempt::where('login_input', $data['login_input'])
    ->whereDate('created_at', today())
    ->count() + 1;
        }

        LoginAttempt::create([
            'user_id'        => optional($data['user'])->id,
            'login_input'    => (string) ($data['login_input'] ?? ''),
            'ip_address'     => $request?->ip() ?? '0.0.0.0',
            'user_agent'     => substr($request?->userAgent() ?? '', 0, 255),
            'outcome'        => $data['outcome'],                // success | user_not_found | invalid_password | blocked
            'attempt_number' => $attemptNumber,
            'is_blocked'     => (bool) ($data['is_blocked'] ?? false),
            'blocked_at'     => $data['blocked_at'] ?? null,
        ]);
    }

    /** Intenta obtener lo que el usuario escribió para loguearse */
    protected function resolveLoginInput(?string $guard, $user): string
    {
        // 1) Si tu login usa RUT y lo tienes en $user->rut
        if ($user && isset($user->rut)) {
            return (string) $user->rut;
        }
        // 2) Si usas email/username
        if ($user && isset($user->email)) {
            return (string) $user->email;
        }
        // 3) Último recurso: tomar del request actual
        return $this->extractLoginInputFromRequest(request());
    }

    /** Extrae login_input desde credentials (Failed) */
    protected function extractLoginInputFromCredentials(array $credentials): string
    {
        // Prioriza campos comunes
        foreach (['rut','email','username','login','user'] as $key) {
            if (!empty($credentials[$key])) {
                return (string) $credentials[$key];
            }
        }
        // Si no, intenta el primero que no sea password/remember
        foreach ($credentials as $k => $v) {
            if (!in_array($k, ['password','remember'], true) && $v) {
                return (string) $v;
            }
        }
        return '';
    }

    /** Extrae login_input desde el request (Lockout o fallback) */
    protected function extractLoginInputFromRequest($request): string
    {
        if (!$request) return '';
        foreach (['rut','email','username','login','user'] as $key) {
            $val = $request->input($key);
            if (!empty($val)) return (string) $val;
        }
        // intenta con todos los inputs menos password/remember
        $all = $request->all();
        foreach ($all as $k => $v) {
            if (!in_array($k, ['password','remember'], true) && !is_array($v) && $v) {
                return (string) $v;
            }
        }
        return '';
    }
}
