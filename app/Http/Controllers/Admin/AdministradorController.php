<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdministradorController extends Controller
{
    public function index()
    {
        $admins = AdminUsuario::orderBy('nombre_completo')->get();

        return view('admin.administradores.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.administradores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:150'],
            'email'           => ['required', 'email', 'max:100', 'unique:admin_usuarios,email'],
            'rut'             => ['required', 'string', 'max:100', 'unique:admin_usuarios,rut'],
            'user'            => ['required', 'string', 'max:100', 'unique:admin_usuarios,user'],
            'rol'             => ['required', 'string', 'max:30'],
            'especialidad'    => ['nullable', 'string', 'max:100'],
            'password'        => ['required', 'confirmed', 'min:8'],
            'activo'          => ['nullable', 'boolean'],
        ]);

        $admin = new AdminUsuario();
        $admin->nombre_completo = $validated['nombre_completo'];
        $admin->email           = $validated['email'];
        $admin->rut             = $validated['rut'];
        $admin->user            = $validated['user'];
        $admin->rol             = $validated['rol'];
        $admin->especialidad    = $validated['especialidad'] ?? null;
        $admin->password_hash   = Hash::make($validated['password']);
        $admin->activo          = $request->boolean('activo'); // checkbox
        $admin->save();

        return redirect()
            ->route('admin.administradores.index')
            ->with('success', 'Administrador creado correctamente.');
    }

    public function edit(AdminUsuario $administrador)
    {
        return view('admin.administradores.edit', ['admin' => $administrador]);
    }

    public function update(Request $request, AdminUsuario $administrador)
    {
        $validated = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:150'],
            'email'           => ['required', 'email', 'max:100', Rule::unique('admin_usuarios', 'email')->ignore($administrador->id)],
            'rut'             => ['required', 'string', 'max:100', Rule::unique('admin_usuarios', 'rut')->ignore($administrador->id)],
            'user'            => ['required', 'string', 'max:100', Rule::unique('admin_usuarios', 'user')->ignore($administrador->id)],
            'rol'             => ['required', 'string', 'max:30'],
            'especialidad'    => ['nullable', 'string', 'max:100'],
            'password'        => ['nullable', 'confirmed', 'min:8'],
            'activo'          => ['nullable', 'boolean'],
        ]);

        $administrador->nombre_completo = $validated['nombre_completo'];
        $administrador->email           = $validated['email'];
        $administrador->rut             = $validated['rut'];
        $administrador->user            = $validated['user'];
        $administrador->rol             = $validated['rol'];
        $administrador->especialidad    = $validated['especialidad'] ?? null;
        $administrador->activo          = $request->boolean('activo');

        if (!empty($validated['password'])) {
            $administrador->password_hash = Hash::make($validated['password']);
        }

        $administrador->save();

        return redirect()
            ->route('admin.administradores.index')
            ->with('success', 'Administrador actualizado correctamente.');
    }

    public function destroy(AdminUsuario $administrador)
    {
        $administrador->delete();

        return redirect()
            ->route('admin.administradores.index')
            ->with('success', 'Administrador eliminado correctamente.');
    }

    public function toggle(AdminUsuario $administrador)
    {
        $administrador->activo = !$administrador->activo;
        $administrador->save();

        return redirect()
            ->route('admin.administradores.index')
            ->with('success', 'Estado actualizado.');
    }
}
