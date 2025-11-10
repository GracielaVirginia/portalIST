<?php

namespace App\Http\Controllers;

use App\Models\BloodPressure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BloodPressureController extends Controller
{
    public function store(Request $request)
    {
        Log::info('[BP] → Iniciando store()', [
            'expectsJson' => $request->expectsJson(),
            'headers' => [
                'Accept' => $request->header('Accept'),
                'Content-Type' => $request->header('Content-Type'),
            ],
            'input' => $request->all(),
        ]);

        try {
            // A) Usuario autenticado
            $userId = auth()->id();
            Log::info('[BP] Usuario detectado', ['user_id' => $userId]);

            if (!$userId) {
                Log::warning('[BP] ❌ Usuario no autenticado');
                if ($request->expectsJson()) {
                    return response()->json(['ok' => false, 'reason' => 'unauthenticated'], 401);
                }
                return redirect()->route('login');
            }

            // B) Validación
            $data = $request->validate([
                'fecha'              => ['required', 'date'],
                'tension_sistolica'  => ['required', 'integer', 'between:50,250'],
                'tension_diastolica' => ['required', 'integer', 'between:30,150'],
                'nota'               => ['nullable', 'string', 'max:255'],
            ]);

            Log::info('[BP] Datos validados correctamente', ['data' => $data]);

            // C) Upsert
            DB::enableQueryLog();

            $pressure = BloodPressure::updateOrCreate(
                ['user_id' => $userId, 'fecha' => $data['fecha']],
                [
                    'sistolica'  => (int) $data['tension_sistolica'],
                    'diastolica' => (int) $data['tension_diastolica'],
                    'nota'       => $data['nota'] ?? null,
                ]
            );

            $queries = DB::getQueryLog();
            Log::info('[BP] Query ejecutada', $queries);
            Log::info('[BP] Registro resultante', $pressure->toArray());

            $created = $pressure->wasRecentlyCreated;

            // D) Respuesta según tipo
            if ($request->expectsJson()) {
                return response()->json([
                    'ok'        => true,
                    'message'   => $created ? 'Tensión creada' : 'Tensión actualizada',
                    'id'        => $pressure->id,
                    'fecha'     => optional($pressure->fecha)->toDateString(),
                    'sistolica' => (int) $pressure->sistolica,
                    'diastolica'=> (int) $pressure->diastolica,
                    'nota'      => $pressure->nota,
                    'created'   => $created,
                ], $created ? 201 : 200);
            }

            return back()->with('ok', $created ? 'Tensión creada' : 'Tensión actualizada');

        } catch (ValidationException $e) {
            Log::error('[BP] ⚠️ Error de validación', ['errors' => $e->errors()]);
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'reason' => 'validation', 'errors' => $e->errors()], 422);
            }
            throw $e;
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('[BP] ❌ Error de base de datos', [
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'msg' => $e->getMessage(),
            ]);
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'reason' => 'db', 'message' => $e->getMessage()], 500);
            }
            throw $e;
        } catch (\Throwable $e) {
            Log::error('[BP] 💥 Error inesperado', ['msg' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'reason' => 'unexpected', 'message' => $e->getMessage()], 500);
            }
            throw $e;
        }
    }
}
