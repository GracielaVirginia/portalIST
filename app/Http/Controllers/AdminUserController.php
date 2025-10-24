<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{


    /**
     * Lista de todos los usuarios
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.users', compact('users'));
    }

    /**
     * Búsqueda de usuarios
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $users = User::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->orWhere('rut', 'LIKE', "%{$query}%")
                    ->limit(10)
                    ->get(['id', 'name', 'email', 'rut', 'locked_until', 'login_attempts']);
        
        return response()->json($users);
    }

    /**
     * Desbloquear usuario
     */
    public function unlock(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        
        $user->update([
            'locked_until' => null,
            'login_attempts' => 0
        ]);

        return back()->with('success', "Usuario {$user->name} desbloqueado correctamente.");
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            // Eliminar registros relacionados primero
            \App\Models\LoginLog::where('user_id', $user->id)->delete();
            
            // Eliminar usuario
            $user->delete();
        });

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
        }

        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}