<?php
namespace App\Http\Controllers;

use App\Models\WeightEntry;
use Illuminate\Http\Request;

class WeightEntryController extends Controller
{
public function store(Request $request)
{
    // 1) Validación
    $data = $request->validate([
        'fecha' => ['required', 'date'],
        'peso'  => ['required', 'numeric', 'between:20,400'],
        'nota'  => ['nullable', 'string', 'max:255'],
        // 'paciente_id' => ['nullable','exists:pacientes,id'], // si aplica
    ], [
        'peso.between' => 'El peso debe estar entre 20 y 400 kg.',
    ]);

    // (Opcional) Normaliza a 1 decimal si usas step="0.1"
    $valorPeso = round((float) $data['peso'], 1);

    // 2) Upsert por usuario+fecha (idempotente)
    $entry = \App\Models\WeightEntry::updateOrCreate(
        ['user_id' => auth()->id(), 'fecha' => $data['fecha']],
        ['valor'   => $valorPeso, 'nota' => $data['nota'] ?? null]
    );

    $created = $entry->wasRecentlyCreated;

    // 3) Respuesta según el tipo de petición
    if ($request->expectsJson()) {
        return response()->json([
            'ok'      => true,
            'message' => $created ? 'Peso creado' : 'Peso actualizado',
            'id'      => $entry->id,
            'fecha'   => $entry->fecha instanceof \Illuminate\Support\Carbon
                           ? $entry->fecha->toDateString()
                           : (string) $entry->fecha,
            'peso'    => $entry->valor,
            'nota'    => $entry->nota,
            'created' => $created,
        ], $created ? 201 : 200);
    }

    // Fallback: navegación tradicional
    return back()->with('success', $created ? 'Peso creado' : 'Peso actualizado');
}
}
