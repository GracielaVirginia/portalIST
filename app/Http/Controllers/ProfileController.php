<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    /**
     * Mostrar formulario de edición de perfil del usuario autenticado.
     * GET /profile  →  route('profile.edit')
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Actualizar datos del perfil del usuario.
     * PATCH /profile  →  route('profile.update')
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'Perfil actualizado correctamente.');
    }

    /**
     * Eliminar la cuenta del usuario autenticado.
     * DELETE /profile  →  route('profile.destroy')
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Tu cuenta ha sido eliminada correctamente.');
    }
}
