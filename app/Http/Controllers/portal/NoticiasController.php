<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Noticia;

class NoticiasController extends Controller
{
    public function index()
    {
        // IMPORTANTE: devolvemos MODELOS, no arrays
        $noticias = Noticia::orderByDesc('created_at')->get();
        return view('portal.noticias.index', compact('noticias'));
    }

    public function show($id)
    {
        $n = Noticia::findOrFail($id);

        // Tu vista show usa array; si prefieres, puedes pasar el modelo.
        return view('portal.noticias.show', ['noticia' => [
            'id'     => $n->id,
            'titulo' => $n->titulo,
            'bajada' => $n->bajada,
            'imagen' => $n->imagen_url, // accessor en el modelo
        ]]);
        // Alternativa si la vista espera modelo:
        // return view('portal.noticias.show', compact('n'));
    }
}
