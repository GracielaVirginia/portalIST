<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GestionSaludCompleta;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\PortalValidacionConfig; 
use App\Models\Image;
use App\Services\AlertaService;
class AuthController extends Controller
{
    // GET /login
public function showLoginForm()
{
    $seleccionada = Image::where('seleccionada', true)->first();
    $imagenLoginUrl = $seleccionada
        ? asset('images/' . $seleccionada->nombre)
        : asset('images/bg-purple.png'); // fallback

    return view('auth.login', compact('imagenLoginUrl'));
}

    // POST /login
    public function login(Request $request, AlertaService $alertas)
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
    // --- ALERTA cuando llega a 2 o más fallos (antes de bloquear)
    if ($intentos >= 2) {
        $alertas->registrar('login_fallido', [
            'user_id'   => $user->id,
            'intentos'  => $intentos,
            'documento' => $rut,
            'extra'     => ['mensaje' => 'Intentos de login con RUT >= 2'],
        ]);
    }
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
        // --- ALERTA de usuario bloqueado (si usas el UserObserver, puedes omitir este log para evitar duplicados)
        $alertas->registrar('usuario_bloqueado', [
            'user_id'   => $user->id,
            'documento' => $rut,
            'extra'     => ['motivo' => '3 intentos fallidos con RUT'],
        ]);
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
public function loginPasaporte(Request $request, AlertaService $alertas)
{
    // ===== 0) Validación básica =====
    Log::info('[LOGIN PPT] Método invocado', [
        'ip'      => $request->ip(),
        'payload' => $request->only('pasaporte'), // nunca loguees la pass
    ]);

    $request->validate([
        'pasaporte'         => ['required','string','max:25'],
        'passworPasaporte'  => ['required','string'],
    ]);

    // ===== 1) Normalización =====
    $pasaporteRaw = strtoupper(trim($request->pasaporte));              // visible
    $pasaporteNorm = preg_replace('/[^A-Z0-9]/', '', $pasaporteRaw);    // comparaciones
    $passwordIngresada = $request->passworPasaporte;

    Log::info('[LOGIN PPT] Normalización', [
        'raw'  => $pasaporteRaw,
        'norm' => $pasaporteNorm,
    ]);

    // ===== 2) Verificar existencia del paciente en gestiones (tolerante) =====
    $existePaciente = \DB::table('gestiones_salud_completa')
        ->whereIn(\DB::raw("UPPER(TRIM(tipo_documento))"), ['PASAPORTE','PASSPORT','PASAP','PPT','RUT'])
        ->whereRaw("
            UPPER(REPLACE(REPLACE(REPLACE(numero_documento,'-',''),' ',''),'.','')) = ?
        ", [$pasaporteNorm])
        ->exists();

    Log::info('[LOGIN PPT] Existe en gestiones?', ['exists' => $existePaciente]);

    if (!$existePaciente) {
        Log::warning('[LOGIN PPT] Paciente NO encontrado en gestiones', [
            'search_norm' => $pasaporteNorm,
        ]);
        return back()
            ->withErrors(['pasaporte' => 'Paciente no encontrado.'])
            ->onlyInput('pasaporte');
    }

    // ===== 3) Buscar o crear el usuario en users =====
    // IMPORTANTE: usa la columna REAL que tengas en users.
    // Si NO tienes 'pasaporte' en users y usas 'rut' para todo:
    //   - reemplaza 'pasaporte' => $pasaporteRaw por 'rut' => $pasaporteRaw en todo este bloque y en Auth::attempt.
    $user = \App\Models\User::where('rut', $pasaporteRaw)->first();

    Log::info('[LOGIN PPT] Usuario buscado por pasaporte', [
        'columna' => 'users.pasaporte',
        'found'   => (bool) $user,
    ]);

    if (!$user) {
        $registro = \DB::table('gestiones_salud_completa')
            ->select('nombre_paciente', 'email', 'lugar_cita', 'tipo_documento', 'numero_documento', 'created_at')
            ->whereIn(\DB::raw("UPPER(TRIM(tipo_documento))"), ['PASAPORTE','PASSPORT','PASAP','PPT','RUT'])
            ->whereRaw("
                UPPER(REPLACE(REPLACE(REPLACE(numero_documento,'-',''),' ',''),'.','')) = ?
            ", [$pasaporteNorm])
            ->orderBy('created_at','asc')
            ->first();

        $nombre     = $registro->nombre_paciente ?? ('Paciente '.$pasaporteRaw);
        $email      = $registro->email ?? null;
        $lugar_cita = $registro->lugar_cita ?? null;

        $defaultPlain = substr($pasaporteNorm, 0, 5); // 5 primeros alfanum normalizados

        Log::info('[LOGIN PPT] Creando usuario nuevo', [
            'name'   => $nombre,
            'email'  => $email,
            'seed'   => $defaultPlain,
        ]);

        $user = \App\Models\User::create([
            'rut'             => $pasaporteRaw,          // <<< si NO existe esta columna, usa 'rut' => $pasaporteRaw
            'name'                  => $nombre,
            'email'                 => $email,
            'password'              => \Illuminate\Support\Facades\Hash::make($defaultPlain),
            'lugar_cita'            => $lugar_cita,
            'password_needs_change' => true,
            'is_blocked'            => false,
            'failed_login_attempts' => 0,
            'is_validated'          => false,
            'theme'                 => 'blue',
        ]);

        Log::info('[LOGIN PPT] Usuario creado', ['user_id' => $user->id]);
    }

    // ===== 4) Bloqueado? =====
    if ($user->is_blocked) {
        Log::warning('[LOGIN PPT] Usuario bloqueado', ['user_id' => $user->id]);
        return back()->withErrors([
            'pasaporteFeedback' => 'Tu cuenta está bloqueada por múltiples intentos fallidos.',
        ])->onlyInput('pasaporte');
    }

    // ===== 5) Intento de autenticación =====
    // OJO: Si no tienes 'pasaporte' en users, cambia a ['rut' => $pasaporteRaw, 'password' => ...]
    $credenciales = ['rut' => $pasaporteRaw, 'password' => $passwordIngresada];
    Log::info('[LOGIN PPT] Auth::attempt con columna', ['key' => 'pasaporte']);

    if (!\Illuminate\Support\Facades\Auth::attempt($credenciales)) {
        $intentos = ($user->failed_login_attempts ?? 0) + 1;
        $user->failed_login_attempts = $intentos;
    // --- ALERTA cuando llega a 2 o más fallos (antes de bloquear)
    if ($intentos >= 2) {
        $alertas->registrar('login_fallido', [
            'user_id'   => $user->id,
            'intentos'  => $intentos,
            'documento' => $pasaporteRaw,
            'extra'     => ['mensaje' => 'Intentos de login con PASAPORTE >= 2'],
        ]);
    }
        if ($intentos >= 3) {
            $user->is_blocked  = true;
            $user->blocked_at  = now();
            $user->save();

            Log::warning('[LOGIN PPT] Cuenta bloqueada por intentos', [
                'user_id'  => $user->id,
                'intentos' => $intentos,
            ]);
            

            return back()->withErrors([
                'passworPasaporte' => 'Cuenta bloqueada por intentos fallidos. Contacta soporte.',
            ])->onlyInput('pasaporte');
        }

        $user->save();

        $restantes = 3 - $intentos;
        Log::notice('[LOGIN PPT] Password incorrecta', [
            'user_id'   => $user->id,
            'restantes' => $restantes,
        ]);

        return back()->withErrors([
            'passworPasaporte' => "Contraseña incorrecta. Te quedan {$restantes} intento(s).",
        ])->onlyInput('pasaporte');
    }

    // ===== 6) Éxito: reset intentos =====
    $user->update([
        'failed_login_attempts' => 0,
        'is_blocked'            => false,
    ]);

    // ===== 7) Forzar cambio de contraseña si aplica =====
    $defaultPlain = substr($pasaporteNorm, 0, 5);
    if (\Illuminate\Support\Facades\Hash::check($defaultPlain, $user->password) || $user->force_password_change) {
        session(['forcePasswordChange' => true]);
    }

    Log::debug('[LOGIN PPT] forcePasswordChange', ['value' => session('forcePasswordChange')]);

    // ===== 8) Redirecciones por validación =====
    $modo = \App\Models\PortalValidacionConfig::where('activo', true)->first();

    Log::info('[LOGIN PPT] Estado de validación', [
        'user_id'      => $user->id,
        'doc_guardado' => $user->pasaporte ?? $user->rut ?? null,
        'is_validated' => (bool)($user->is_validated ?? false),
        'modo_activo'  => $modo ? $modo->only(['id','slug','nombre']) : null,
    ]);

    if (!$user->is_validated) {
        $destRouteName = null;
        if ($modo) {
            if ($modo->slug === 'sin-validacion' || $modo->id == 1) $destRouteName = 'validacion.sin';
            elseif ($modo->slug === 'numero-caso' || $modo->id == 2) $destRouteName = 'validacion.caso';
            elseif ($modo->slug === 'tres-opciones' || $modo->id == 3) $destRouteName = 'validacion.tres';
            elseif ($modo->slug === 'crear-cuenta' || $modo->id == 4) $destRouteName = 'validacion.cuenta';
        }
        if (!$destRouteName) $destRouteName = 'portal.home';

        Log::warning('[LOGIN PPT] Redirigiendo por validación pendiente', [
            'dest_route' => $destRouteName,
            'dest_url'   => route($destRouteName),
        ]);

        return redirect()->route($destRouteName)
            ->with('success', 'Bienvenido. Completa la validación.');
    }

    Log::warning('[LOGIN PPT] Redirigiendo al portal (validado)', [
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

            return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
        }

        return back()->with('warning', 'Aún no has iniciado sesión.');
    }
}
