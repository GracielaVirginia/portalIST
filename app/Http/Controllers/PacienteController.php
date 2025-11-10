<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\GestionSaludCompleta;
use App\Models\User;

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

        // 🔹 Buscar paciente en gestiones
        Log::channel('daily')->info('[VERIFICAR RUT] Ejecutando consulta SQL:', [
            'tabla' => 'gestiones_salud_completa',
            'condiciones' => ['tipo_documento' => 'RUT', 'numero_documento' => $rut]
        ]);

        $paciente = GestionSaludCompleta::where('tipo_documento', 'RUT')
            ->where('numero_documento', $rut)
            ->orderBy('created_at', 'asc')
            ->first();
        if ($paciente) {
        $usuario = User::where('rut', $rut)->first();
        if ($usuario && $usuario->is_blocked) {
          return response()->json(['exists' => false, 'bloqueado' => true, 'message' => 'Paciente Bloqueado. Comunicate con el Administrador']);
        }
    }
        if (!$paciente) {
            Log::channel('daily')->warning('[VERIFICAR RUT] Paciente NO encontrado ❌', ['rut' => $rut]);
            return response()->json(['exists' => false, 'message' => 'Paciente no registrado.']);
        }

        // 🔹 Buscar si el usuario existe y está bloqueado
        $usuario = User::where('rut', $rut)->first();

        if ($usuario && $usuario->is_blocked) {
            Log::channel('daily')->warning('[VERIFICAR RUT] Usuario bloqueado 🚫', [
                'rut' => $rut,
                'blocked_at' => $usuario->blocked_at,
                
            ]);

            return response()->json([
                'exists' => true,
                'blocked' => true,
                'message' => 'Tu cuenta está bloqueada. Comunícate con el administrador o soporte.',
            ]);
        }

        // 🔹 Paciente válido y sin bloqueo
        Log::channel('daily')->info('[VERIFICAR RUT] Paciente encontrado ✅', [
            'nombre' => $paciente->nombre_paciente,
            'rut'    => $rut
        ]);

        return response()->json([
            'exists' => true,
            'blocked' => false,
            // 'name'   => $paciente->nombre_paciente,
            'message'=> 'Paciente encontrado correctamente.',
        ]);
    }

public function verificarPasaporte(Request $request)
{
    // 1) Normaliza input
    $pasaporteRaw = strtoupper(trim($request->input('pasaporte', '')));
    $pasaporteNorm = preg_replace('/[^A-Z0-9]/', '', $pasaporteRaw); // quita espacios, puntos y guiones

    // 2) Valida formato básico (ajusta rango si lo requieres)
    if (!preg_match('/^[A-Z0-9-\. ]{4,20}$/', $pasaporteRaw)) {
        Log::info('[PASAPORTE] Formato inválido', ['raw' => $pasaporteRaw]);
        return response()->json(['exists' => false, 'message' => 'Formato de Pasaporte inválido.']);
    }

    // 3) Busca en gestiones de forma tolerante (may/min, espacios, guiones, puntos)
    //    Acepta variantes de tipo_documento por si en BD no está exactamente "PASAPORTE"
    $paciente = \DB::table('gestiones_salud_completa')
        ->whereIn(\DB::raw("UPPER(TRIM(tipo_documento))"), ['PASAPORTE','PASSPORT','PASAP','PPT','RUT'])
        ->whereRaw("
            REPLACE(
              REPLACE(
                REPLACE(UPPER(TRIM(numero_documento)),'-',''),
              ' ', ''),
            '.', '') = ?
        ", [$pasaporteNorm])
        ->orderBy('created_at','asc')
        ->first();

    Log::info('[PASAPORTE] Comparación', [
        'raw'          => $pasaporteRaw,
        'norm'         => $pasaporteNorm,
        'encontrado'   => (bool)$paciente,
        'ej_tipo'      => $paciente->tipo_documento ?? null,
        'ej_numero'    => $paciente->numero_documento ?? null,
    ]);

    if (!$paciente) {
        // (Opcional) Log ayuda rápida para diagnosticar “parecidos”
        $candidatos = \DB::table('gestiones_salud_completa')
            ->select('id','tipo_documento','numero_documento')
            ->whereIn(\DB::raw("UPPER(TRIM(tipo_documento))"), ['PASAPORTE','PASSPORT','PASAP','PPT','RUT'])
            ->whereRaw("
                UPPER(REPLACE(REPLACE(REPLACE(numero_documento,'-',''),' ',''),'.','')) LIKE ?
            ", ['%'.substr($pasaporteNorm,0,5).'%'])
            ->limit(5)
            ->get();

        Log::warning('[PASAPORTE] No match; candidatos cercanos', [
            'buscado_norm' => $pasaporteNorm,
            'candidatos'   => $candidatos,
        ]);

        return response()->json(['exists' => false, 'message' => 'Paciente no registrado.']);
    }

    // 4) Chequea bloqueo en users (si tienes columna 'pasaporte', usa esa; si no, fallback a 'rut')
    $usuario =  \App\Models\User::where('rut', $pasaporteRaw)->first();

    if ($usuario && $usuario->is_blocked) {
        Log::info('[PASAPORTE] Usuario bloqueado', ['doc' => $pasaporteRaw, 'user_id' => $usuario->id ?? null]);
        return response()->json([
            'exists'    => false,
            'bloqueado' => true,
            'blocked'   => true,
            'message'   => 'Paciente Bloqueado. Comunícate con el Administrador',
        ]);
    }

    // 5) OK
    Log::info('[PASAPORTE] Paciente encontrado', [
        'doc'    => $pasaporteRaw,
        'nombre' => $paciente->nombre_paciente ?? null,
    ]);

    return response()->json([
        'exists'    => true,
        'bloqueado' => false,
        'blocked'   => false,
        'message'   => 'Paciente encontrado correctamente.',
    ]);
}

}
