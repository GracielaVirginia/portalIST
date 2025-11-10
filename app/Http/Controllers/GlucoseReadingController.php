<?php
namespace App\Http\Controllers;

use App\Models\GlucoseReading;
use Illuminate\Http\Request;

class GlucoseReadingController extends Controller
{
public function store(Request $request)
{
    // 1) Validación (Laravel maneja JSON y form-data igual)
    $data = $request->validate([
        'fecha'   => ['required', 'date'],
        'glucosa' => ['required', 'integer', 'between:40,600'],
        'nota'    => ['nullable', 'string', 'max:255'],
        // 'paciente_id' => ['nullable','exists:pacientes,id'], // si aplica
    ], [
        'glucosa.between' => 'La glucosa debe estar entre 40 y 600 mg/dL.',
    ]);

    // 2) Upsert por usuario+fecha (idempotente)
    $reading = \App\Models\GlucoseReading::updateOrCreate(
        ['user_id' => auth()->id(), 'fecha' => $data['fecha']],
        ['valor'   => $data['glucosa'], 'nota'  => $data['nota'] ?? null]
    );

    // 3) Respuesta según el tipo de petición
    $created = $reading->wasRecentlyCreated;

    // Si el front envía fetch con Accept: application/json, devolvemos JSON
    if ($request->expectsJson()) {
        return response()->json([
            'ok'       => true,
            'message'  => $created ? 'Glucosa creada' : 'Glucosa actualizada',
            'id'       => $reading->id,
            'fecha'    => $reading->fecha->toDateString(), // asegúrate que sea cast date en el modelo
            'glucosa'  => $reading->valor,
            'nota'     => $reading->nota,
            'created'  => $created,
        ], $created ? 201 : 200);
    }

    // Fallback: navegación tradicional
    return back()->with('ok', $created ? 'Glucosa creada' : 'Glucosa actualizada');
}
}
