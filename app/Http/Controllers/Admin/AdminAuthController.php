<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUsuario;

class AdminAuthController extends Controller
{
    /**
     * Muestra el formulario de login del administrador.
     */
    public function showLoginFormAdmin()
    {
        return view('admin.login');
    }

    /**
     * Procesa el intento de login (POST /login-admin)
     */
public function loginAttemp(Request $request)
{
    $credentials = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ], [
        'username.required' => 'Ingresa tu usuario o email.',
        'password.required' => 'Ingresa tu contraseña.',
    ]);

    $input      = trim($credentials['username']);
    $password   = $credentials['password'];
    $rutPosible = strtoupper(str_replace(['.', ' '], '', $input));

    // Buscar por user | email | rut
    $admin = AdminUsuario::where('user', $input)
        ->orWhere('email', $input)
        ->orWhere('rut', $rutPosible)
        ->first();

// ❌ No existe en la tabla admin_usuarios
if (!$admin) {
    \Log::warning('Intento de acceso no autorizado: usuario no existe en admin_usuarios', [
        'input' => $input,
        'ip' => $request->ip(),
        'time' => now()->toDateTimeString(),
    ]);

    // Devuelve vista con alerta SweetAlert amigable
    return back()
        ->with('alert', [
            'icon' => 'info',
            'title' => 'Acceso restringido',
            'text'  => 'Este acceso es solo para administradores. 
                        Si eres paciente, por favor inicia sesión en el Portal Pacientes.',
            'footer'=> '<a href="'.route('login').'">Ir al Portal Pacientes</a>'
        ])
        ->onlyInput('username');
}


    // ❌ Usuario inactivo
    if (!$admin->activo) {
        \Log::warning('Intento de acceso con usuario inactivo', [
            'user' => $admin->user,
            'email' => $admin->email,
            'ip' => $request->ip(),
            'time' => now()->toDateTimeString(),
        ]);

        return back()->withErrors([
            'username' => 'Tu usuario administrativo está inactivo.',
        ])->onlyInput('username');
    }

    // ❌ Contraseña incorrecta
    if (!Hash::check($password, $admin->password_hash)) {
        \Log::warning('Intento fallido de login (contraseña incorrecta)', [
            'user' => $admin->user,
            'email' => $admin->email,
            'ip' => $request->ip(),
            'time' => now()->toDateTimeString(),
        ]);

        return back()->withErrors([
            'password' => 'La contraseña ingresada es incorrecta.',
        ])->onlyInput('username');
    }

    // ✅ Acceso correcto
    $request->session()->regenerate();
    $request->session()->put('admin_usuario_id', $admin->id);

    $admin->ultimo_login = now();
    $admin->save(['timestamps' => false]);

    \Log::info('Inicio de sesión ADMIN exitoso', [
        'user' => $admin->user,
        'email' => $admin->email,
        'ip' => $request->ip(),
        'time' => now()->toDateTimeString(),
    ]);

    return redirect()->route('admin.dashboard')->with('ok', 'Bienvenido.');
}

    /**
     * Cierra sesión del administrador.
     */
    public function logout(Request $request)
    {
        $request->session()->forget('admin_usuario_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')->with('ok', 'Sesión cerrada.');
    }
}
