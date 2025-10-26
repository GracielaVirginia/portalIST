<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserPasswordController extends Controller
{
    public function update(Request $request)
    {
        // 🧪 Log de entrada
        logger()->debug('[PW] Inicio update()', [
            'route' => $request->route()?->getName(),
            'user_id' => $request->user()?->id ?? null,
        ]);

        // ⚠️ Usuario nulo (sesión caída)
        $user = $request->user();
        if (!$user) {
            logger()->warning('[PW] Usuario no autenticado al intentar cambiar password.');
            return redirect()->route('login')->withErrors([
                'auth' => 'Tu sesión expiró. Inicia sesión nuevamente.',
            ]);
        }

        // ✅ Validación
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password'     => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',     // al menos una mayúscula
                'regex:/[0-9]/',     // al menos un número
                'confirmed',         // requiere new_password_confirmation
            ],
        ], [
            'new_password.min'       => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.regex'     => 'La nueva contraseña debe incluir al menos una mayúscula y un número.',
            'new_password.confirmed' => 'La confirmación no coincide.',
        ]);

        // ❌ Contraseña actual incorrecta
        if (!Hash::check($data['current_password'], $user->password)) {
            logger()->info('[PW] current_password inválida', ['user_id' => $user->id]);
            throw ValidationException::withMessages([
                'current_password' => 'La contraseña actual no es correcta.',
            ])->redirectTo(url()->previous());
        }

        // ❌ Evita reutilizar la misma contraseña
        if (Hash::check($data['new_password'], $user->password)) {
            logger()->info('[PW] nueva contraseña igual a la vigente', ['user_id' => $user->id]);
            throw ValidationException::withMessages([
                'new_password' => 'La nueva contraseña no puede ser igual a la actual.',
            ])->redirectTo(url()->previous());
        }

        try {
            DB::beginTransaction();

            // 🔒 Actualiza password y apaga el flag
            $user->forceFill([
                'password' => Hash::make($data['new_password']),
                'password_needs_change' => false, // => 0
            ])->save();

            // (Opcional) Cerrar sesiones en otros dispositivos
            // Auth::logoutOtherDevices($data['new_password']);

            DB::commit();
            logger()->debug('[PW] Cambio OK', [
                'user_id' => $user->id,
                'needs_change' => $user->password_needs_change,
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            logger()->error('[PW] Error guardando password', [
                'user_id' => $user->id,
                'ex' => $e->getMessage(),
            ]);
            return back()->withErrors([
                'general' => 'No pudimos actualizar tu contraseña. Intenta nuevamente.',
            ]);
        }

        // Limpia indicador en sesión (si existía)
        $request->session()->forget('forcePasswordReason');

        // Re-genera el ID de sesión y token CSRF por seguridad
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('portal.home')
            ->with('success', 'Contraseña actualizada correctamente.');
    }
}
