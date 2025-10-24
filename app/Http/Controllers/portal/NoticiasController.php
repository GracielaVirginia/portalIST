<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Noticia;

class NoticiasController extends Controller
{
    public function index()
    {
        $noticias = Noticia::orderByDesc('created_at')->get();

        // genera url para cada una
        $noticias = $noticias->map(function ($n) {
            return [
                'id'      => $n->id,
                'titulo'  => $n->titulo,
                'bajada'  => $n->bajada,
                'imagen'  => $n->imagen,
                'url'     => route('portal.noticias.show', $n->id),
            ];
        });

        return view('portal.noticias.index', ['noticias' => $noticias]);
    }

    public function show($id)
    {
        $noticia = Noticia::findOrFail($id);

        return view('portal.noticias.show', ['noticia' => $noticia]);
    }
}
