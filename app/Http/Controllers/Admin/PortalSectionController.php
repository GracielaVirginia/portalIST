<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PortalSectionController extends Controller
{
    /**
     * Lista bloques (con filtros mínimos)
     * ?pagina=conoce-mas
     */
    public function index(Request $request)
    {
        $page = $request->query('pagina', 'conoce-mas');

        $sections = PortalSection::where('page_slug', $page)
            ->orderBy('posicion')
            ->orderBy('id')
            ->get();

        // En una vista admin podrías mostrar tabla + botones (crear/editar/eliminar/ordenar/toggle).
        return view('admin.portal.sections.index', compact('sections', 'page'));
    }

    /**
     * Crear nuevo bloque
     * (Esqueleto: el admin luego edita contenido)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'page_slug'       => 'required|string|max:50',
            'tipo'            => 'required|string|max:50', // hero, beneficios, etc.
            'titulo'          => 'nullable|string|max:255',
            'subtitulo'       => 'nullable|string|max:255',
            'contenido'       => 'nullable|array',         // se guarda como JSON (casts)
            'visible'         => 'nullable|boolean',
            'publicar_desde'  => 'nullable|date',
            'publicar_hasta'  => 'nullable|date|after_or_equal:publicar_desde',
        ]);

        $maxPos = PortalSection::where('page_slug', $data['page_slug'])->max('posicion') ?? 0;

        $section = PortalSection::create([
            'page_slug'      => $data['page_slug'],
            'tipo'           => $data['tipo'],
            'titulo'         => $data['titulo']      ?? null,
            'subtitulo'      => $data['subtitulo']   ?? null,
            'contenido'      => $data['contenido']   ?? null,
            'posicion'       => $maxPos + 1,
            'visible'        => $data['visible']     ?? true,
            'publicar_desde' => $data['publicar_desde'] ?? null,
            'publicar_hasta' => $data['publicar_hasta'] ?? null,
            'updated_by'     => Auth::guard('admin')->id() ?? Auth::id(),
        ]);

        return redirect()
            ->route('admin.portal.sections.index', ['pagina' => $section->page_slug])
            ->with('success', 'Bloque creado.');
    }

    /**
     * Editar bloque (form admin)
     */
    public function edit(PortalSection $section)
    {
        // Muestra el form de edición (texto, JSON, visibilidad, fechas).
        return view('admin.portal.sections.edit', compact('section'));
    }

    /**
     * Actualizar bloque
     */
    public function update(Request $request, PortalSection $section)
    {
        $data = $request->validate([
            'titulo'          => 'nullable|string|max:255',
            'subtitulo'       => 'nullable|string|max:255',
            'contenido'       => 'nullable|array',
            'visible'         => 'nullable|boolean',
            'publicar_desde'  => 'nullable|date',
            'publicar_hasta'  => 'nullable|date|after_or_equal:publicar_desde',
        ]);

        $section->fill([
            'titulo'         => $data['titulo']      ?? $section->titulo,
            'subtitulo'      => $data['subtitulo']   ?? $section->subtitulo,
            'contenido'      => $data['contenido']   ?? $section->contenido,
            'visible'        => $data['visible']     ?? $section->visible,
            'publicar_desde' => $data['publicar_desde'] ?? $section->publicar_desde,
            'publicar_hasta' => $data['publicar_hasta'] ?? $section->publicar_hasta,
            'updated_by'     => Auth::guard('admin')->id() ?? Auth::id(),
        ])->save();

        return back()->with('success', 'Bloque actualizado.');
    }

    /**
     * Eliminar bloque
     */
    public function destroy(PortalSection $section)
    {
        $page = $section->page_slug;
        $section->delete();

        // Recompactar posiciones (opcional)
        $rest = PortalSection::where('page_slug', $page)->orderBy('posicion')->get();
        $i = 1;
        foreach ($rest as $s) {
            $s->update(['posicion' => $i++]);
        }

        return redirect()->route('admin.portal.sections.index', ['pagina' => $page])
            ->with('success', 'Bloque eliminado.');
    }

    /**
     * Toggle de visibilidad rápido
     */
    public function toggleVisible(PortalSection $section)
    {
        $section->update([
            'visible'    => !$section->visible,
            'updated_by' => Auth::guard('admin')->id() ?? Auth::id(),
        ]);

        return back()->with('success', 'Visibilidad actualizada.');
    }

    /**
     * Reordenar bloques: recibe un array de IDs en el orden deseado
     * body: { order: [5,3,9, ...] , page_slug: 'conoce-mas' }
     */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'page_slug' => 'required|string|max:50',
            'order'     => 'required|array',
            'order.*'   => 'integer|exists:portal_sections,id',
        ]);

        DB::transaction(function () use ($data) {
            $pos = 1;
            foreach ($data['order'] as $id) {
                PortalSection::where('id', $id)
                    ->where('page_slug', $data['page_slug'])
                    ->update(['posicion' => $pos++]);
            }
        });

        return back()->with('success', 'Orden actualizado.');
    }
}
