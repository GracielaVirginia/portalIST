<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promocion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;  
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Str;

class PromocionController extends Controller
{
    public function index()
    {
        $promos = Promocion::orderBy('orden')->get();
        // Vista: resources/views/admin/promociones/index.blade.php
        return view('admin.promociones.index', compact('promos'));
    }

    public function create()
    {
        // Vista: resources/views/admin/promociones/create.blade.php
        return view('admin.promociones.create');
    }

public function store(Request $request)
{
    try {
        Log::info('[Promocion@store] Inicio', [
            'input' => $request->except(['imagen', '_token', '_method']),
            'has_file' => $request->hasFile('imagen'),
        ]);

        // 1) Validación
        $validated = $request->validate($this->rules());
        $data = $validated;
        $data['activo']    = $request->boolean('activo');
        $data['destacada'] = $request->boolean('destacada');

        // 2) Subida de imagen (si viene)
if ($request->hasFile('imagen')) {
    $file = $request->file('imagen');
    if (!$file->isValid()) {
        return back()->with('error', 'La imagen no es válida.');
    }

    // Crea el nombre y la ruta correcta
    $filename = time().'_'.$file->getClientOriginalName();
    $destination = public_path('images/promo');

    // Si no existe la carpeta, créala
    if (!is_dir($destination)) {
        mkdir($destination, 0775, true);
    }

    // Mueve el archivo
    $file->move($destination, $filename);

    // Guarda el path relativo (para asset())
    $data['imagen_path'] = 'images/promo/'.$filename;
}


        // 3) Orden al final
        $data['orden'] = (Promocion::max('orden') ?? 0) + 1;

        // 4) Crear + destacar exclusiva en transacción
        $promo = null;
        DB::beginTransaction();

        $promo = Promocion::create($data);
        Log::info('[Promocion@store] Creada', ['id' => $promo->id, 'destacada' => $data['destacada']]);

        if ($data['destacada']) {
            Promocion::where('destacada', true)->where('id', '!=', $promo->id)->update(['destacada' => false]);
            $promo->update(['destacada' => true, 'activo' => true]);
            Log::info('[Promocion@store] Marcada como destacada (exclusiva)', ['id' => $promo->id]);
        }

        DB::commit();
        Log::info('[Promocion@store] OK', ['id' => $promo->id]);

        return redirect()->route('admin.promociones.index')->with('success', 'Promoción creada.');
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('[Promocion@store] ERROR', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            // 'trace' => $e->getTraceAsString(), // activa solo si necesitas el stack completo
        ]);
        return back()->withInput()->with('error', 'No se pudo guardar la promoción: ' . $e->getMessage());
    }
}

    public function edit(Promocion $promocion)
    {
        // Vista: resources/views/admin/promociones/edit.blade.php
        return view('admin.promociones.edit', compact('promocion'));
    }


public function update(Request $request, Promocion $promocion)
{
    try {
        // 1) Validación (usa tus rules(true))
        $data = $request->validate($this->rules(update: true));

        // 2) Normaliza checkboxes
        $data['activo']    = $request->boolean('activo');
        $data['destacada'] = $request->boolean('destacada');

        // 3) Si viene nueva imagen -> guardar en public/promos y actualizar imagen_path
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            if (!$file->isValid()) {
                return back()->withInput()->with('error', 'La imagen no es válida.');
            }

            // nombre de archivo seguro
            $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext      = $file->getClientOriginalExtension();
            $filename = time().'_'.Str::slug($original).'.'.$ext;

            // mover a /public/promos
            $dest = public_path('images/promos');
            if (!is_dir($dest)) {
                @mkdir($dest, 0775, true);
            }
            $file->move($dest, $filename);

            // opcional: borrar imagen anterior si existía y es un archivo físico en public/
            if (!empty($promocion->imagen_path)) {
                $old = public_path($promocion->imagen_path);
                if (str_starts_with($promocion->imagen_path, 'images/promos/') && file_exists($old)) {
                    @unlink($old);
                }
            }

            $data['imagen_path'] = 'images/promos/'.$filename; // se mostrará con asset($promocion->imagen_path)
        }

        DB::beginTransaction();

        // 4) Actualiza campos base
        $promocion->update($data);

        // 5) Manejo de "destacada" exclusiva
        if ($data['destacada']) {
            // Desmarcar las demás
            Promocion::where('id', '!=', $promocion->id)
                ->where('destacada', true)
                ->update(['destacada' => false]);
            // Asegurar activa
            $promocion->update(['destacada' => true, 'activo' => true]);
        }

        DB::commit();
        return redirect()->route('admin.promociones.index')->with('success', 'Promoción actualizada.');
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('[Promocion@update] ERROR', ['msg' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        return back()->withInput()->with('error', 'No se pudo actualizar: '.$e->getMessage());
    }
}

    public function destroy(Promocion $promocion)
    {
        $promocion->delete();
        return back()->with('success', 'Promoción eliminada.');
    }

    // Activar/Desactivar rápido
    public function toggle(Promocion $promocion)
    {
        $promocion->activo = ! $promocion->activo;

        // si se desactiva la destacada, asegúrate que no quede destacada inactiva
        if (! $promocion->activo && $promocion->destacada) {
            $promocion->destacada = false;
        }

        $promocion->save();
        return back()->with('success', 'Estado actualizado.');
    }

    // Marca como destacada EXCLUSIVA (las demás quedan en false)
    public function destacar(Promocion $promocion)
    {
        $this->marcarExclusiva($promocion->id);
        return back()->with('success', 'Promoción marcada como destacada.');
    }

    private function rules(bool $update = false): array
    {
        return [
            'titulo'         => ['required', 'string', 'max:160'],
            'subtitulo'      => ['nullable', 'string', 'max:200'],
            'contenido_html' => ['nullable', 'string'],
            'imagen_path'    => ['nullable', 'string', 'max:255'],
        'cta_texto'      => ['nullable', 'string', 'max:60'],
        'cta_url'        => ['nullable', 'string', 'max:255'],            'activo'         => ['sometimes', 'boolean'],
            'destacada'      => ['sometimes', 'boolean'],
            // 'orden' se maneja internamente (o con UI de arrastrar si agregas luego)
        ];
    }

    // Garantiza UNA sola destacada y activa
    private function marcarExclusiva(int $id): void
    {
        DB::transaction(function () use ($id) {
            Promocion::where('destacada', true)->update(['destacada' => false]);
            Promocion::whereKey($id)->update(['destacada' => true, 'activo' => true]);
        });
    }
}
