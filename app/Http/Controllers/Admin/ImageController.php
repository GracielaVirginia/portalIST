<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    /**
     * Devuelve todas las imágenes (para vista o galería)
     */
    public function index()
    {
        $imagenes = Image::orderBy('nombre')->get();
        return response()->json($imagenes);
    }

    /**
     * Guarda una nueva imagen subida (desde el botón “+ Subir imagen”)
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp,svg|max:4096',
        ]);

        try {
            // Nombre único
            $file = $request->file('image');
            $nombre = time() . '_' . $file->getClientOriginalName();

            // Guardar en /public/images
            $path = public_path('images');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $file->move($path, $nombre);

            // Registrar en BD
            $img = Image::create(['nombre' => $nombre]);

            Log::info('[ImageController@store] Imagen subida', ['nombre' => $nombre]);

            // Respuesta JSON para el fetch
            return response()->json([
                'id' => $img->id,
                'nombre' => $img->nombre,
                'url' => asset('images/' . $img->nombre),
            ]);
        } catch (\Throwable $e) {
            Log::error('[ImageController@store] Error al subir imagen', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'No se pudo subir la imagen.'], 500);
        }
    }

    /**
     * Elimina una imagen del sistema
     */
    public function destroy(Image $image)
    {
        try {
            $path = public_path('images/' . $image->nombre);

            if (File::exists($path)) {
                File::delete($path);
            }

            $image->delete();

            Log::info('[ImageController@destroy] Imagen eliminada', ['nombre' => $image->nombre]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('[ImageController@destroy] Error al eliminar', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'No se pudo eliminar la imagen.'], 500);
        }
    }
    public function select(\App\Models\Image $image)
{
    \App\Models\Image::where('seleccionada', true)->update(['seleccionada' => false]); // opcional
    $image->seleccionada = true;
    $image->save();
    return response()->json(['ok' => true, 'id' => $image->id]);
}
}
