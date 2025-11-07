<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function keepAlive(Request $request)
    {
        // Actualiza un valor de sesión para extender su tiempo
        $request->session()->put('last_keepalive_at', now()->toDateTimeString());
        return response()->json(['ok' => true]);
    }
}
