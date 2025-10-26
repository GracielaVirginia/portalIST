<?php

namespace App\Http\Controllers;

use App\Models\ExamenNombre;
use App\Models\Especialidad;
use Illuminate\Http\Request;

class ExamenNombreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $examenes = ExamenNombre::with('especialidad')->get();
        return view('admin.examen_nombre.index', compact('examenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $especialidades = Especialidad::all();
        return view('admin.examen_nombre.create', compact('especialidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:20',
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|string|max:20',
            'especialidad_id' => 'required|exists:especialidads,id',
        ]);

        ExamenNombre::create($request->all());

        return redirect()->route('admin.examen_nombre.index')
                         ->with('success', 'Examen creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamenNombre $examenNombre)
    {
        return view('admin.examen_nombre.show', compact('examenNombre'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamenNombre $examenNombre)
    {
        $especialidades = Especialidad::all();
        return view('admin.examen_nombre.edit', compact('examenNombre', 'especialidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamenNombre $examenNombre)
    {
        $request->validate([
            'codigo' => 'required|string|max:20',
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|string|max:20',
            'especialidad_id' => 'required|exists:especialidads,id',
        ]);

        $examenNombre->update($request->all());

        return redirect()->route('admin.examen_nombre.index')
                         ->with('success', 'Examen actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamenNombre $examenNombre)
    {
        $examenNombre->delete();

        return redirect()->route('admin.examen_nombre.index')
                         ->with('success', 'Examen eliminado correctamente.');
    }
}
