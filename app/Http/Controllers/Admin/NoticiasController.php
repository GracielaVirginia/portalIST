<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoticiasController extends Controller
{
    public function index()
    {
        $noticias = Noticia::orderByDesc('created_at')->get();
        return view('admin.noticias.index', compact('noticias'));
    }

    public function create()
    {
        return view('admin.noticias.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'    => 'required|string|max:255',
            'bajada'    => 'nullable|string',
            'contenido' => 'nullable|string',
            'imagen'    => 'nullable|image|max:3072',
            'destacada' => 'nullable|boolean',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('noticias', 'public');
        }

        $noticia = Noticia::create($data);

        if (!empty($data['destacada'])) {
            Noticia::where('id', '<>', $noticia->id)->update(['destacada' => false]);
        }

        return redirect()->route('admin.noticias.index')->with('ok', 'Noticia creada');
    }

    public function edit(Noticia $noticia)
    {
        return view('admin.noticias.edit', compact('noticia'));
    }

    public function update(Request $request, Noticia $noticia)
    {
        $data = $request->validate([
            'titulo'    => 'required|string|max:255',
            'bajada'    => 'nullable|string',
            'contenido' => 'nullable|string',
            'imagen'    => 'nullable|image|max:3072',
            'destacada' => 'nullable|boolean',
        ]);

        if ($request->hasFile('imagen')) {
            if ($noticia->imagen && Storage::disk('public')->exists($noticia->imagen)) {
                Storage::disk('public')->delete($noticia->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('noticias', 'public');
        }

        $noticia->update($data);

        if (!empty($data['destacada'])) {
            Noticia::where('id', '<>', $noticia->id)->update(['destacada' => false]);
        }

        return redirect()->route('admin.noticias.index')->with('ok', 'Noticia actualizada');
    }

    public function destroy(Noticia $noticia)
    {
        if ($noticia->imagen && Storage::disk('public')->exists($noticia->imagen)) {
            Storage::disk('public')->delete($noticia->imagen);
        }
        $noticia->delete();

        return back()->with('ok', 'Noticia eliminada');
    }

public function toggleDestacada(Request $request, Noticia $noticia)
{
    Noticia::where('id', '<>', $noticia->id)->update(['destacada' => false]);
    $noticia->update(['destacada' => true]);

    if ($request->expectsJson()) {
        return response()->json(['ok' => true, 'id' => $noticia->id]);
    }
    return back()->with('ok', 'Noticia puesta en el home.');
}

}
