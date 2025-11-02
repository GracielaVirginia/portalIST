<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewsController extends Controller
{
    /**
     * Crea o actualiza la review del usuario (UPSERT).
     * Si envías anonimo=true y tu DB permite user_id NULL, se guarda sin usuario.
     * Aun así marcamos el flag en users para no volver a mostrar el popup.
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user(); // requiere middleware auth

            $data = $request->validate([
                'rating'  => ['required','integer','between:1,5'],
                'comment' => ['nullable','string','max:2000'],
                'anonimo' => ['nullable','boolean'],
            ]);

            $anonimo = (bool)($data['anonimo'] ?? false);

            if ($anonimo) {
                // ⚠️ Para permitir esto, tu migración debe tener user_id nullable.
                $review = Review::create([
                    'user_id' => null,
                    'anonimo' => true,
                    'rating'  => (int)$data['rating'],
                    'comment' => $data['comment'] ?? null,
                ]);
            } else {
                // UPSERT por usuario (evita 409)
                $review = Review::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'anonimo' => false,
                        'rating'  => (int)$data['rating'],
                        'comment' => $data['comment'] ?? null,
                    ]
                );
            }

            // Marca el flag en users para no mostrar el popup otra vez
            if (property_exists($user, 'review') || array_key_exists('review', $user->getAttributes())) {
                if (!$user->review) {
                    $user->review = true;
                    $user->save();
                }
            }

            return response()->json([
                'ok'      => true,
                'message' => '¡Gracias por tu calificación!',
                'review'  => $review->only('id','user_id','anonimo','rating','comment'),
            ], 200);

        } catch (\Throwable $e) {
            Log::error('[reviews.store] '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'ok'      => false,
                'message' => 'Error guardando la calificación.',
            ], 500);
        }
    }

    /**
     * Actualiza la review propia (si no existe, la crea).
     */
    public function updateMine(Request $request)
    {
        try {
            $user = $request->user();

            $data = $request->validate([
                'rating'  => ['required','integer','between:1,5'],
                'comment' => ['nullable','string','max:2000'],
                'anonimo' => ['nullable','boolean'],
            ]);

            $review = Review::where('user_id', $user->id)->latest('id')->first();

            if (!$review) {
                $review = Review::create([
                    'user_id' => $user->id,
                    'anonimo' => (bool)($data['anonimo'] ?? false),
                    'rating'  => (int)$data['rating'],
                    'comment' => $data['comment'] ?? null,
                ]);
            } else {
                $review->update([
                    'anonimo' => (bool)($data['anonimo'] ?? $review->anonimo),
                    'rating'  => (int)$data['rating'],
                    'comment' => $data['comment'] ?? null,
                ]);
            }

            if (property_exists($user, 'review') || array_key_exists('review', $user->getAttributes())) {
                if (!$user->review) {
                    $user->review = true;
                    $user->save();
                }
            }

            return response()->json([
                'ok'      => true,
                'message' => 'Calificación actualizada.',
                'review'  => $review->only('id','user_id','anonimo','rating','comment'),
            ], 200);

        } catch (\Throwable $e) {
            Log::error('[reviews.updateMine] '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'ok'      => false,
                'message' => 'Error actualizando la calificación.',
            ], 500);
        }
    }
}
