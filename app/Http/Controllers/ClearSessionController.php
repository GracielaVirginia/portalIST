<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClearSessionController extends Controller
{
    public function clearSession(Request $request)
    {
        // Invalida la sesión actual
        $request->session()->invalidate();

        // Regenera el token CSRF
        $request->session()->regenerateToken();

        // Redirige a donde desees
        return redirect('/login')->with('status', 'La sesión del navegador ha sido cerrada.');
    }
}
