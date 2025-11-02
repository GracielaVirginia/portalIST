<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutConfirmController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // Si ya calificó, no muestres popup: sal directo
        if ((int)($user->review ?? 0) === 1) {
            return $this->logout($request);
        }

        // Renderiza vista que abre SweetAlert automáticamente
        return view('auth.logout-confirm', [
            'storeUrl' => route('reviews.store'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/'); // o a donde quieras
    }
}
