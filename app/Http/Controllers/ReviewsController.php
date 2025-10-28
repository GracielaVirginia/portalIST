<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user(); // obligatorio por middleware

        $data = $request->validate([
            'rating'  => ['required','integer','min:1','max:5'],
            'comment' => ['nullable','string','max:2000'],
            'anonimo' => ['nullable','boolean'],
        ]);

        $anonimo = (bool)($data['anonimo'] ?? false);

        // Si NO es anónimo: bloquear duplicado por usuario (también lo evita el índice unique)
        if (!$anonimo) {
            $yaTiene = Review::where('user_id', $user->id)->exists() || (bool)$user->review;
            if ($yaTiene) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Ya registraste una calificación.',
                ], 409);
            }
        }

        // Crear review
        $review = Review::create([
            'user_id' => $anonimo ? null : $user->id,
            'anonimo' => $anonimo,
            'rating'  => (int)$data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        // Marcar flag en users siempre que exista usuario autenticado
        // (aunque sea anónimo, queremos no volver a mostrar el popup)
        if (!$user->review) {
            $user->review = true;
            $user->save();
        }

        return response()->json([
            'ok'     => true,
            'review' => $review->only('id','anonimo','rating','comment'),
            'message' => '¡Gracias por tu calificación!',

        ]);
    }
}
