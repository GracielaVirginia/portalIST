<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ControlesSeriesController extends Controller
{
    public function index(Request $request)
    {
        try {
            // 0) sanity check de auth
            $user = $request->user();
            if (!$user) {
                Log::warning('[controles.series] user=null (no autenticado)');
                return response()->json(['ok' => false, 'error' => 'Unauthenticated'], 401);
            }

            // 1) entrada
            $data = $request->validate([
                'start_date' => ['nullable','date'],
                'end_date'   => ['nullable','date','after_or_equal:start_date'],
            ]);

            $start = $data['start_date'] ?? now()->subDays(30)->toDateString();
            $end   = $data['end_date']   ?? now()->toDateString();

            Log::info('[controles.series] params', [
                'user_id' => $user->id,
                'start'   => $start,
                'end'     => $end,
            ]);

            // 2) query bruta (sin map) para ver si trae filas
            $bpQuery = $user->bloodPressures()->whereBetween('fecha', [$start, $end])->orderBy('fecha','asc');
            $glQuery = $user->glucoseReadings()->whereBetween('fecha', [$start, $end])->orderBy('fecha','asc');
            $weQuery = $user->weightEntries()->whereBetween('fecha', [$start, $end])->orderBy('fecha','asc');

            $bpRaw = $bpQuery->get(['id','fecha','sistolica','diastolica']);
            $glRaw = $glQuery->get(['id','fecha','valor']);
            $weRaw = $weQuery->get(['id','fecha','valor']);

            Log::info('[controles.series] counts', [
                'bp' => $bpRaw->count(),
                'gl' => $glRaw->count(),
                'we' => $weRaw->count(),
            ]);

            // 3) mapeo tolerante a null/string en fecha
            $tension = $bpRaw->map(function($r){
                $f = $r->fecha instanceof \DateTimeInterface
                        ? $r->fecha->toDateString()
                        : ( $r->fecha ? Carbon::parse($r->fecha)->toDateString() : null );
                return [
                    'fecha'      => $f,
                    'sistolica'  => is_null($r->sistolica)  ? null : (int)$r->sistolica,
                    'diastolica' => is_null($r->diastolica) ? null : (int)$r->diastolica,
                ];
            })->filter(fn($r) => !empty($r['fecha']))->values();

            $glucosa = $glRaw->map(function($r){
                $f = $r->fecha instanceof \DateTimeInterface
                        ? $r->fecha->toDateString()
                        : ( $r->fecha ? Carbon::parse($r->fecha)->toDateString() : null );
                return [
                    'fecha' => $f,
                    'valor' => is_null($r->valor) ? null : (int)$r->valor,
                ];
            })->filter(fn($r) => !empty($r['fecha']))->values();

            $peso = $weRaw->map(function($r){
                $f = $r->fecha instanceof \DateTimeInterface
                        ? $r->fecha->toDateString()
                        : ( $r->fecha ? Carbon::parse($r->fecha)->toDateString() : null );
                return [
                    'fecha' => $f,
                    'valor' => is_null($r->valor) ? null : (float)$r->valor,
                ];
            })->filter(fn($r) => !empty($r['fecha']))->values();

            Log::info('[controles.series] mapped counts', [
                'bp' => $tension->count(),
                'gl' => $glucosa->count(),
                'we' => $peso->count(),
            ]);

            return response()->json([
                'ok'      => true,
                'tension' => $tension,
                'glucosa' => $glucosa,
                'peso'    => $peso,
            ]);

        } catch (\Throwable $e) {
            Log::error('[controles.series] exception', [
                'msg'   => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
                'trace' => collect($e->getTrace())->take(5)->all(), // resumen
            ]);

            // En desarrollo, ayuda ver el mensaje exacto en la respuesta:
            if (config('app.debug')) {
                return response()->json([
                    'ok'    => false,
                    'error' => $e->getMessage(),
                ], 500);
            }

            return response()->json(['ok' => false, 'error' => 'Server error'], 500);
        }
    }
}
