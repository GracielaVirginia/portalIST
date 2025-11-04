<?php

namespace App\Http\Controllers;

use App\Models\Profesional;
use App\Models\Sucursal;
use App\Models\TipoProfesional;
use Illuminate\Http\Request;

class ProfesionalController extends Controller
{
    public function index()
    {
        $profesionales = Profesional::with(['sucursal', 'tipoProfesional'])
            ->orderBy('idsucursal')
            ->orderBy('nombres')
            ->get();

        return view('admin.profesionales.index', compact('profesionales'));
    }

    public function create()
    {
        $sucursales = Sucursal::orderBy('orden')->orderBy('nombre')->get(['id','nombre']);
        $tipos      = TipoProfesional::orderBy('idsucursal')->orderBy('nombre')->get(['id','nombre','idsucursal']);
        $profesional = new Profesional();

        return view('admin.profesionales.create', compact('sucursales','tipos','profesional'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idsucursal'          => 'required|exists:sucursales,id',
            'tipo_profesional_id' => 'required|exists:tipos_profesionales,id',
            'nombres'             => 'required|string|max:120',
            'apellidos'           => 'nullable|string|max:120',
            'rut'                 => 'nullable|string|max:30',
            'telefono'            => 'nullable|string|max:60',
            'email'               => 'nullable|email|max:150',
            'notas'               => 'nullable|string|max:255',
        ]);

        $data['idempresa'] = 1;

        Profesional::create($data);

        return redirect()->route('profesionales.index')->with('success', 'Profesional creado.');
    }

    public function edit(Profesional $profesional)
    {
        $sucursales = Sucursal::orderBy('orden')->orderBy('nombre')->get(['id','nombre']);
        $tipos      = TipoProfesional::orderBy('idsucursal')->orderBy('nombre')->get(['id','nombre','idsucursal']);

        return view('admin.profesionales.edit', compact('sucursales','tipos','profesional'));
    }

    public function update(Request $request, Profesional $profesional)
    {
        $data = $request->validate([
            'idsucursal'          => 'required|exists:sucursales,id',
            'tipo_profesional_id' => 'required|exists:tipos_profesionales,id',
            'nombres'             => 'required|string|max:120',
            'apellidos'           => 'nullable|string|max:120',
            'rut'                 => 'nullable|string|max:30',
            'telefono'            => 'nullable|string|max:60',
            'email'               => 'nullable|email|max:150',
            'notas'               => 'nullable|string|max:255',
        ]);

        $profesional->update($data);

        return redirect()->route('profesionales.index')->with('success', 'Profesional actualizado.');
    }

    public function destroy(Profesional $profesional)
    {
        $profesional->delete();

        return redirect()->route('profesionales.index')->with('success', 'Profesional eliminado.');
    }
}
