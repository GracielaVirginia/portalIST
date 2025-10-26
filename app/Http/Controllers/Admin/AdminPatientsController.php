<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminPatientsController extends Controller
{
    // GET /admin/pacientes/lookup?rut=XXXXXXXX-X
    public function lookup(Request $request)
    {
        $rut = $request->string('rut')->toString();
        if (!$rut) {
            return response()->json(['ok'=>false,'error'=>'Falta rut'], 422);
        }

        // Ajusta nombres de columnas según tu esquema
        $user = User::query()
            ->where('rut', $rut)
            ->select('id','name','rut','is_blocked','blocked_at')
            ->first();

        if (!$user) {
            return response()->json([
                'ok' => true,
                'exists' => false,
                'rut' => $rut,
            ]);
        }

        return response()->json([
            'ok' => true,
            'exists' => true,
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'apellido'  => $user->apellido,
                'rut'       => $user->rut,
                'blocked'   => (int)$user->is_blocked === 1,
                'blocked_at'=> $user->blocked_at,
            ],
        ]);
    }

    // POST /admin/users/{user}/unblock
    public function unblock(User $user)
    {
        $user->is_blocked = 0;    // ajusta si el nombre difiere
        $user->failed_login_attempts =0;
        $user->failed_validated_attempts = 0;
        $user->is_validated = 0;
        $user->force_password_change = 1;
        $user->blocked_at  = null;
        $user->save();

        return response()->json(['ok'=>true]);
    }

    // DELETE /admin/users/{user}
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['ok'=>true]);
    }
}
