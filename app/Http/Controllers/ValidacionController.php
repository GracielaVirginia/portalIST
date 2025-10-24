<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValidacionController extends Controller
{
    // GET /validacion
    public function index(Request $request)
    {
        $user = $request->user();

        return view('validacion', [
            'intentosFallidos' => (int) ($user->intentos_validacion ?? 0),
            'bloqueado'        => (bool) ($user->is_blocked ?? false),
            'validado'         => (bool) ($user->validado ?? false),
        ]);
    }

    // POST /validacion
    public function store(Request $request)
    {
        $rid = uniqid('VALID_', true);
        Log::info("[$rid] === INICIO ValidacionController@store ===", [
            'ip' => $request->ip(),
            'ua' => $request->userAgent(),
            'time' => now()->toDateTimeString(),
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error_message', 'No autenticado.');
        }

        $user = $request->user();

        // --- si está bloqueado ---
        $intentosFallidos = (int) ($user->failed_validated_attempts ?? 0);
        if ($user->is_blocked || $intentosFallidos >= 3) {
            Log::warning("[$rid] Usuario bloqueado de validación", ['user_id' => $user->id]);
            return redirect()->route('login')
                ->with('error_message', 'Tu cuenta ha sido bloqueada. Contacta al administrador.');
        }

        // --- limpia el input ---
        $numero = preg_replace('/\D+/', '', (string) $request->input('numero_caso', ''));
        if (strlen($numero) < 1) {
            return back()->withErrors(['numero_caso' => 'Debes ingresar tu N° de caso.'])->withInput();
        }

        Log::info("[$rid] Validando número de caso", ['numero' => $numero, 'user_id' => $user->id]);

        // --- valida en la tabla local ---
        try {
            $exists = DB::table('gestiones_salud_completa')
                ->where('numero_caso', $numero)
                ->exists();
        } catch (\Throwable $e) {
            Log::error("[$rid] Error consultando la base local", [
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            return back()->withErrors(['validacion' => 'Error consultando la base de datos local.'])->withInput();
        }

        if ($exists) {
            // ✅ correcto
            $user->is_validated = 1;
            $user->failed_validated_attempts = 0;
            $user->is_blocked = false;
            $user->save();

            Log::info("[$rid] VALIDACIÓN OK", ['user_id' => $user->id]);
            return redirect()->route('portal.home')->with('success', 'Validación exitosa.');
        }

        // ❌ incorrecto → sumar intento
        $intentosFallidos++;
        $user->failed_validated_attempts = $intentosFallidos;
        if ($intentosFallidos >= 3) {
            $user->is_blocked = true;
        }
        $user->save();

        Log::warning("[$rid] VALIDACIÓN FALLÓ", [
            'user_id' => $user->id,
            'numero' => $numero,
            'intentos' => $intentosFallidos,
            'bloqueado' => (bool) $user->is_blocked,
        ]);

        if ($user->is_blocked) {
            return redirect()->route('login')
                ->with('error_message', 'Has agotado los intentos y tu cuenta fue bloqueada.');
        }

        return back()->withErrors([
            'validacion' => "N° de caso incorrecto. Intentos fallidos: {$intentosFallidos} de 3.",
        ])->withInput();
    }
}
