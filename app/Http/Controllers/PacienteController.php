<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\GestionSaludCompleta;

class PacienteController extends Controller
{
    public function verificarRut(Request $request)
    {
        $rut = strtoupper(trim($request->input('rut', '')));

        // 🔹 LOG 1 — RUT recibido desde el frontend
        Log::channel('daily')->info('[VERIFICAR RUT] RUT recibido desde el cliente:', ['rut' => $rut]);

        // 🔹 Validar formato básico (12345678-9 o 12345678-K)
        if (!preg_match('/^[0-9]+-[0-9K]$/', $rut)) {
            Log::channel('daily')->warning('[VERIFICAR RUT] Formato inválido', ['rut' => $rut]);
            return response()->json(['exists' => false, 'message' => 'Formato de RUT inválido.']);
        }

        // 🔹 LOG 2 — Antes de la consulta SQL
        Log::channel('daily')->info('[VERIFICAR RUT] Ejecutando consulta SQL:', [
            'tabla' => 'gestiones_salud_completa',
            'condiciones' => [
                'tipo_documento' => 'RUT',
                'numero_documento' => $rut
            ]
        ]);

        $paciente = \App\Models\GestionSaludCompleta::where('tipo_documento', 'RUT')
            ->where('numero_documento', $rut)
            ->orderBy('created_at', 'asc')
            ->first();

        // 🔹 LOG 3 — Resultado de la consulta
        if ($paciente) {
            Log::channel('daily')->info('[VERIFICAR RUT] Paciente encontrado ✅', [
                'nombre' => $paciente->nombre_paciente,
                'rut'    => $rut
            ]);
            return response()->json([
                'exists' => true,
                'name'   => $paciente->nombre_paciente,
            ]);
        } else {
            Log::channel('daily')->warning('[VERIFICAR RUT] Paciente NO encontrado ❌', [
                'rut' => $rut
            ]);
            return response()->json(['exists' => false, 'message' => 'Paciente no registrado.']);
        }
    }
}
