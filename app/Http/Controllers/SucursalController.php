<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SucursalController extends Controller
{
    // 📋 Lista todas las sucursales
    public function index()
    {
        $sucursales = Sucursal::orderBy('orden')->get();
        return view('admin.sucursales.index', compact('sucursales'));
    }

    // ➕ Formulario de creación
    public function create()
    {
        $sucursal = new Sucursal();
        return view('admin.sucursales.create', compact('sucursal'));
    }

    // 💾 Guardar nueva sucursal
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'     => 'required|string|max:255',
            'ciudad'     => 'nullable|string|max:100',
            'region'     => 'nullable|string|max:100',
            'direccion'  => 'nullable|string|max:255',
            'telefono'   => 'nullable|string|max:100',
            'email'      => 'nullable|email|max:255',
        ]);

        $validated['idempresa'] = 1;
        $validated['slug'] = Str::slug($request->nombre);

        Sucursal::create($validated);

        return redirect()->route('sucursales.index')
                         ->with('success', 'Sucursal creada correctamente.');
    }

    // ✎ Formulario de edición
    public function edit(Sucursal $sucursal)
    {
        return view('admin.sucursales.edit', compact('sucursal'));
    }

    // 🔁 Actualizar sucursal
    public function update(Request $request, Sucursal $sucursal)
    {
        $validated = $request->validate([
            'nombre'     => 'required|string|max:255',
            'ciudad'     => 'nullable|string|max:100',
            'region'     => 'nullable|string|max:100',
            'direccion'  => 'nullable|string|max:255',
            'telefono'   => 'nullable|string|max:100',
            'email'      => 'nullable|email|max:255',
        ]);

        $validated['slug'] = Str::slug($request->nombre);

        $sucursal->update($validated);

        return redirect()->route('sucursales.index')
                         ->with('success', 'Sucursal actualizada correctamente.');
    }

    // 🗑️ Eliminar sucursal
    public function destroy(Sucursal $sucursal)
    {
        $sucursal->delete();

        return redirect()->route('sucursales.index')
                         ->with('success', 'Sucursal eliminada correctamente.');
    }

    // 👁️ Cambiar visibilidad (toggle)
    public function toggle(Sucursal $sucursal)
    {
        $sucursal->visible = !$sucursal->visible;
        $sucursal->save();

        return response()->json(['visible' => $sucursal->visible]);
    }

    // ↕️ Reordenar (AJAX)
    public function reorder(Request $request)
    {
        $ids = $request->input('orden', []);
        foreach ($ids as $index => $id) {
            Sucursal::where('id', $id)->update(['orden' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }
}
