<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeSectionSettingsController extends Controller
{
    /** Muestra el formulario */
public function edit()
{
    $tipo = $this->get('home_section_tipo', 'banner');

    // Banner: textos + imagen resuelta a URL para preview
    $banner = [
        'titulo' => $this->get('home_banner_titulo', '¡Conoce las nuevas funcionalidades del Portal Salud IST!'),
        'texto'  => $this->get('home_banner_texto', 'Accede desde tu celular y gestiona tus atenciones médicas fácilmente.'),
        'cta'    => $this->get('home_banner_cta', 'Descúbrelo aquí →'),
        'url'    => $this->get('home_banner_url', '/promociones'),
    ];

    $imgPath = $this->get('home_banner_image', null);
    $imgUrl  = null;
    if ($imgPath) {
        // http/https → usar tal cual; images/* → public/images; otro → storage/app/public/*
        $imgUrl = \Illuminate\Support\Str::startsWith($imgPath, ['http://','https://'])
            ? $imgPath
            : (\Illuminate\Support\Str::startsWith($imgPath, ['images/'])
                ? asset($imgPath)
                : asset('storage/'.$imgPath));
    }
    $banner['img_url'] = $imgUrl;

    // Cards
    $cardsJson = $this->get('home_cards', '[]');
    $cards = json_decode($cardsJson, true) ?: [
        ['icon' => '💬', 'titulo' => 'Atención personalizada', 'texto' => 'Agenda tus consultas de forma rápida y segura.'],
        ['icon' => '🩺', 'titulo' => 'Salud preventiva', 'texto' => 'Programas y controles para tu bienestar.'],
        ['icon' => '📱', 'titulo' => 'Resultados en línea', 'texto' => 'Consulta informes y exámenes cuando quieras.'],
    ];

    // =========================
    // NUEVO: Galería de imágenes
    // =========================
    try {
        // Cargar imágenes desde la tabla 'images' ordenadas por nombre
        $imagenes = \App\Models\Image::ordenPorNombre()->get(['id', 'nombre']);

        // Debug: cantidad y primeros nombres
        \Log::info('[HomeSectionSettingsController@edit] Cargadas imágenes para galería', [
            'count'    => $imagenes->count(),
            'ejemplos' => $imagenes->take(3)->pluck('nombre'),
        ]);
    } catch (\Throwable $e) {
        \Log::error('[HomeSectionSettingsController@edit] Error cargando imágenes', [
            'message' => $e->getMessage(),
        ]);
        $imagenes = collect(); // evita "undefined variable" y permite render vacío
    }

    // Debug del estado general de la vista
    \Log::debug('[HomeSectionSettingsController@edit] Estado inicial', [
        'tipo'        => $tipo,
        'banner'      => $banner,
        'cards_count' => is_array($cards) ? count($cards) : null,
        'imgPath'     => $imgPath,
        'imgUrl'      => $imgUrl,
    ]);

    return view('admin.config-home', compact('tipo', 'banner', 'cards', 'imagenes'));
}


public function update(Request $request)
{
    $request->validate([
        'home_section_tipo' => 'required|in:banner,cards',
    ]);

    $updatedBy = Auth::guard('admin')->id() ?? Auth::id();

    DB::beginTransaction();
    try {
        // Tipo seleccionado (banner|cards)
        $this->put(
            'home_section_tipo',
            $request->home_section_tipo,
            'string',
            'Tipo de bloque inferior en pantalla de inicio (banner o cards)',
            $updatedBy
        );

        if ($request->home_section_tipo === 'banner') {
            // Validación textos + imagen (permitimos SVG con mimes)
            $data = $request->validate([
                'home_banner_titulo' => 'nullable|string|max:255',
                'home_banner_texto'  => 'nullable|string|max:255',
                'home_banner_cta'    => 'nullable|string|max:255',
                'home_banner_url'    => 'nullable|string|max:255',
                'home_banner_image'  => 'nullable|file|mimes:jpg,jpeg,png,webp,svg|max:4096',
            ]);

            // Guardar textos
            $this->put('home_banner_titulo', $data['home_banner_titulo'] ?? '', 'string', 'Título del banner principal', $updatedBy);
            $this->put('home_banner_texto',  $data['home_banner_texto']  ?? '', 'string', 'Texto del banner principal', $updatedBy);
            $this->put('home_banner_cta',    $data['home_banner_cta']    ?? '', 'string', 'Texto del botón CTA del banner', $updatedBy);
            $this->put('home_banner_url',    $data['home_banner_url']    ?? '', 'string', 'Enlace del botón del banner', $updatedBy);

            // ==============================================
            // NUEVO: guardar imagen seleccionada desde galería
            // ==============================================
            if ($request->filled('home_banner_imagen')) {
                $nombre = trim($request->input('home_banner_imagen'));
                $valor = 'images/' . ltrim($nombre, '/');

                // Guardar en system_settings
                $this->put(
                    'imagen-login',
                    $valor,
                    'string',
                    'Imagen de login seleccionada desde galería (/public/images)',
                    $updatedBy
                );

                // Marcar en tabla images (solo una seleccionada)
                try {
                    \App\Models\Image::where('seleccionada', true)->update(['seleccionada' => false]);
                    \App\Models\Image::where('nombre', $nombre)->update(['seleccionada' => true]);
                    \Log::info('[HomeSectionSettingsController@update] Imagen marcada como seleccionada', ['nombre' => $nombre]);
                } catch (\Throwable $e) {
                    \Log::warning('[HomeSectionSettingsController@update] No se pudo actualizar flag seleccionada', ['error' => $e->getMessage()]);
                }
            }

            // ==============================================
            // Guardar imagen subida manualmente (si se cargó archivo)
            // ==============================================
            if ($request->hasFile('home_banner_image')) {
                // borrar anterior si estaba en storage
                $old = $this->get('home_banner_image');
                if ($old && !Str::startsWith($old, ['http://','https://','images/'])) {
                    Storage::disk('public')->delete($old);
                }

                // subir nueva → storage/app/public/banners/...
                $path = $request->file('home_banner_image')->store('banners', 'public');
                $this->put('home_banner_image', $path, 'string', 'Imagen del banner', $updatedBy);
            }

        } else {
            // Cards: normalizamos 3 posiciones
            $cards = $request->input('cards', []);
            for ($i = 0; $i < 3; $i++) {
                $cards[$i] = [
                    'icon'   => $cards[$i]['icon']   ?? '✨',
                    'titulo' => $cards[$i]['titulo'] ?? '',
                    'texto'  => $cards[$i]['texto']  ?? '',
                ];
            }

            $this->put(
                'home_cards',
                json_encode($cards, JSON_UNESCAPED_UNICODE),
                'json',
                'JSON con las tres cards informativas',
                $updatedBy
            );
        }

        DB::commit();
        return back()->with('success', 'Configuración actualizada correctamente.');
    } catch (\Throwable $e) {
        DB::rollBack();
        report($e);
        \Log::error('[HomeSectionSettingsController@update] Error guardando configuración', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        return back()->withErrors(['config' => 'No se pudo guardar la configuración.'])->withInput();
    }
}


    /* =========================
       Helpers de lectura/escritura
       ========================= */

    private function get(string $clave, $default = null)
    {
        $row = DB::table('system_settings')->where('clave', $clave)->first();
        return $row ? $row->valor : $default;
    }

    private function put(string $clave, $valor, string $tipo = 'string', ?string $descripcion = null, $updatedBy = null): void
    {
        // Si viene array, lo convertimos a JSON por seguridad
        if (is_array($valor)) {
            $valor = json_encode($valor, JSON_UNESCAPED_UNICODE);
            $tipo  = 'json';
        }

        DB::table('system_settings')->updateOrInsert(
            ['clave' => $clave],
            [
                'valor'       => $valor,
                'descripcion' => $descripcion,
                'tipo'        => $tipo,
                'updated_by'  => $updatedBy,
                'updated_at'  => now(),
            ]
        );
    }
}
