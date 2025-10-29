<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GestionSaludCompleta;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\PortalValidacionConfig; 

class AuthController extends Controller
{
    // GET /login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // POST /login
    public function login(Request $request)
    {
        $request->validate([
            'rut'      => ['required','string','max:12','regex:/^[0-9]+-[0-9Kk]$/'],
            'password' => ['required','string'],
        ]);

        $rut = strtoupper(trim($request->rut));
        $passwordIngresada = $request->password;

        // 1) Verificar que el paciente exista en gestiones_salud_completa
        $existePaciente = GestionSaludCompleta::where('tipo_documento', 'RUT')
            ->where('numero_documento', $rut)
            ->exists();

        if (!$existePaciente) {
            return back()->withErrors(['rut' => 'Paciente no encontrado.'])->onlyInput('rut');
        }

        // 2) Buscar o crear usuario en users (rut/name/email/password/flags)
        $user = User::where('rut', $rut)->first();

        if (!$user) {
            // Intentar tomar nombre y email desde el primer registro de gestiones
            $registro = GestionSaludCompleta::where('tipo_documento', 'RUT')
                ->where('numero_documento', $rut)
                ->orderBy('created_at', 'asc')
                ->first();

            $nombre = $registro?->nombre_paciente ?? 'Paciente '.$rut;
            $email  = $registro?->email;
            $lugar_cita = $registro?->lugar_cita;
            $rutDigits = preg_replace('/[^0-9]/', '', $rut);
            $defaultPlain = substr($rutDigits, 0, 5);

            $user = User::create([
                'rut'                   => $rut,
                'name'                  => $nombre,
                'email'                 => $email,
                'password'              => Hash::make($defaultPlain),
                'lugar_cita'            => $lugar_cita,
                'password_needs_change' => true,
                'is_blocked'            => false,
                'failed_login_attempts' => 0,
                'is_validated'          => false,
                'theme'                 => 'blue',
            ]);
        }

        // 3) Bloqueado?
        if ($user->is_blocked) {
            return back()->withErrors([
                'rutFeedback' => 'Tu cuenta está bloqueada por múltiples intentos fallidos.',
            ])->onlyInput('rut');
        }

        // 4) Intento de autenticación
        if (!Auth::attempt(['rut' => $rut, 'password' => $passwordIngresada])) {
            $intentos = ($user->failed_login_attempts ?? 0) + 1;

            if ($intentos >= 3) {
                $user->update([
                    'is_blocked'            => true,
                    'blocked_at'            => now(),
                    'failed_login_attempts' => $intentos,
                ]);

                return back()->withErrors([
                    'password' => 'Cuenta bloqueada por intentos fallidos. Contacta soporte.',
                ])->onlyInput('rut');
            }

            $user->update(['failed_login_attempts' => $intentos]);

            $restantes = 3 - $intentos;
            return back()->withErrors([
                'password' => "Contraseña incorrecta. Te quedan {$restantes} intento(s).",
            ])->onlyInput('rut');
        }

        // 5) Éxito: reset intentos y registrar IP
        $user->update([
            'failed_login_attempts' => 0,
            'is_blocked'            => false,
        ]);

        // 6) Forzar cambio de contraseña si corresponde
        $rutDigits = preg_replace('/[^0-9]/', '', $rut);
        $defaultPlain = substr($rutDigits, 0, 5);

        if (Hash::check($defaultPlain, $user->password) || $user->force_password_change) {
            session(['forcePasswordChange' => true]);
        }

        Log::debug('[LOGIN] forcePasswordChange', ['value' => session('forcePasswordChange')]);

// 7) Redirección según validación elegida por el administrador
$modo = PortalValidacionConfig::where('activo', true)->first();

// LOG: estado previo a redirigir
Log::info('[LOGIN PACIENTE] Estado de validación', [
    'user_id'       => $user->id ?? null,
    'rut'           => $user->rut ?? null,
    'is_validated'  => (bool)($user->is_validated ?? false),
    'modo_activo'   => $modo ? $modo->only(['id','slug','nombre']) : null,
]);

if (!$user->is_validated) {
    // calcular destino según slug o id
    $destRouteName = null;

    if ($modo) {
        if ($modo->slug === 'sin-validacion' || $modo->id == 1) {
            $destRouteName = 'validacion.sin';
        } elseif ($modo->slug === 'numero-caso' || $modo->id == 2) {
            $destRouteName = 'validacion.caso';
        } elseif ($modo->slug === 'tres-opciones' || $modo->id == 3) {
            $destRouteName = 'validacion.tres';
        } elseif ($modo->slug === 'crear-cuenta' || $modo->id == 4) {
            $destRouteName = 'validacion.cuenta';
        }
    }

    // fallback si no hay modo activo
    if (!$destRouteName) {
        $destRouteName = 'portal.home';
    }

    $destUrl = route($destRouteName);

    // LOG: a dónde vamos a redirigir
    Log::warning('[LOGIN PACIENTE] Redirigiendo por validación pendiente', [
        'dest_route' => $destRouteName,
        'dest_url'   => $destUrl,
    ]);

    return redirect()->to($destUrl)
        ->with('success', 'Bienvenido. Completa la validación.');
}

// LOG: validado -> va al home del portal
Log::warning('[LOGIN PACIENTE] Redirigiendo al portal (validado)', [
    'dest_route' => 'portal.home',
    'dest_url'   => route('portal.home'),
]);

return redirect()->route('portal.home')
    ->with('success', 'Inicio de sesión exitoso.');
}



    // POST /logout
    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('welcome')->with('success', 'Sesión cerrada correctamente.');
        }

        return back()->with('warning', 'Aún no has iniciado sesión.');
    }
}
