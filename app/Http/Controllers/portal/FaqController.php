<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FaqController extends Controller
{
    public function list(Request $request)
    {
        // ===== Paso 1: registrar inicio =====
        Log::info('📘 [FaqController@list] Método iniciado', [
            'q' => $request->query('q')
        ]);

        try {
            $q = trim((string) $request->query('q', ''));

            // ===== Paso 2: obtener FAQs activas =====
            $faqs = Faq::query()
                ->select(['id','question','answer'])
                ->when($q, function ($query, $q) {
                    $query->where(function ($qq) use ($q) {
                        $qq->where('question', 'like', "%{$q}%")
                           ->orWhere('answer', 'like', "%{$q}%")
                           ->orWhere('category', 'like', "%{$q}%");
                    });
                })
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->limit(200)
                ->get();

            // ===== Paso 3: log de conteo y ejemplo =====
            Log::info('📘 [FaqController@list] FAQs obtenidas', [
                'count' => $faqs->count(),
                'first' => $faqs->first()
            ]);

            // ===== Paso 4: construir respuesta =====
            $response = [
                'ok'    => true,
                'count' => $faqs->count(),
                'items' => $faqs,
            ];

            Log::info('📘 [FaqController@list] Respuesta lista', [
                'status' => 'ok',
                'response_size' => strlen(json_encode($response)),
            ]);

            return response()->json($response);

        } catch (\Throwable $e) {
            // ===== Paso 5: capturar errores =====
            Log::error('❌ [FaqController@list] Error al obtener FAQs', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
