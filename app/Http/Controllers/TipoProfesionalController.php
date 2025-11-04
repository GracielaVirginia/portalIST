<?php

namespace App\Http\Controllers;

use App\Models\TipoProfesional;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class TipoProfesionalController extends Controller
{
    // 📋 Listado principal
    public function index()
    {
        $tipos = TipoProfesional::with('sucursal')
                  ->orderBy('idsucursal')
                  ->orderBy('nombre')
                  ->get();

        return view('admin.tipo-profesional.index', compact('tipos'));
    }

    // ➕ Formulario de creación
    public function create()
    {
        $sucursales = Sucursal::orderBy('orden')->orderBy('nombre')->get(['id','nombre']);
        $tipo = new TipoProfesional();

        return view('admin.tipo-profesional.create', compact('sucursales','tipo'));
    }

    // 💾 Guardar nuevo tipo
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idsucursal' => 'required|exists:sucursales,id',
            'nombre'     => 'required|string|max:100',
            'descripcion'=> 'nullable|string|max:255',
        ]);

        $validated['idempresa'] = 1;

        TipoProfesional::create($validated);

        return redirect()->route('tipos.index')
                         ->with('success', 'Tipo de profesional creado correctamente.');
    }

    // ✎ Formulario de edición
    public function edit(TipoProfesional $tipoProfesional)
    {
        $sucursales = Sucursal::orderBy('orden')->orderBy('nombre')->get(['id','nombre']);
        $tipo = $tipoProfesional;

        return view('admin.tipo-profesional.edit', compact('sucursales','tipo'));
    }

    // 🔁 Actualizar tipo
    public function update(Request $request, TipoProfesional $tipoProfesional)
    {
        $validated = $request->validate([
            'idsucursal' => 'required|exists:sucursales,id',
            'nombre'     => 'required|string|max:100',
            'descripcion'=> 'nullable|string|max:255',
        ]);

        $tipoProfesional->update($validated);

        return redirect()->route('tipos.index')
                         ->with('success', 'Tipo de profesional actualizado correctamente.');
    }

    // 🗑️ Eliminar
    public function destroy(TipoProfesional $tipoProfesional)
    {
        $tipoProfesional->delete();

        return redirect()->route('tipos.index')
                         ->with('success', 'Tipo de profesional eliminado correctamente.');
    }
}
