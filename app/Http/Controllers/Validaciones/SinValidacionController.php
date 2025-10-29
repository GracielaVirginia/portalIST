<?php

namespace App\Http\Controllers\Validaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SinValidacionController extends Controller
{
    /**
     * GET /validacion/sin-validacion
     * Nombre de ruta: validacion.sin
     *
     * - Si viene ?ok=1 -> setea is_validated = 1 (si existe la columna) y redirige a portal.home
     * - Si no, muestra la vista 'validaciones.sin-validacion'
     */
    public function index(Request $request)
    {
        if ($request->boolean('ok')) {
            $user = $request->user();

            // Si no hay usuario, envía a login (por si se llega sin sesión)
            if (!$user) {
                return redirect()->route('login');
            }

            // Marcar validado solo si existe la columna
            if (Schema::hasColumn('users', 'is_validated')) {
                // Evitar escrituras innecesarias si ya estaba validado
                if (!$user->is_validated) {
                    $user->is_validated = true;
                    $user->save();
                }
            }

            // Siempre al home tras confirmar
            return redirect()->route('portal.home');
        }

        // Render de la vista bloqueante
        return view('validaciones.sin-validacion');
    }
}
