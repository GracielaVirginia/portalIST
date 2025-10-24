<?php

namespace App\Http\Middleware;

use App\Models\AdminUsuario;
use Closure;
use Illuminate\Http\Request;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        // 1) Leer el id guardado en sesión por el login (lo haremos en el paso 4)
        $adminId = $request->session()->get('admin_usuario_id');
        if (!$adminId) {
            return redirect()->route('admin.login.form')
                ->with('error', 'Debes iniciar sesión como administrador.');
        }

        // 2) Cargar el admin y validar que exista y esté activo
        $admin = AdminUsuario::find($adminId);
        if (!$admin) {
            $request->session()->forget('admin_usuario_id');
            return redirect()->route('admin.login.form')
                ->with('error', 'Sesión inválida. Vuelve a iniciar sesión.');
        }

        if (!$admin->activo) {
            $request->session()->forget('admin_usuario_id');
            return redirect()->route('admin.login.form')
                ->with('error', 'Tu usuario administrativo está inactivo.');
        }

        // 3) Dejarlo accesible en el request para controladores/vistas
        $request->attributes->set('adminUsuario', $admin);

        return $next($request);
    }
}
