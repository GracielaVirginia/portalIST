<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserDocumentController extends Controller
{
    /**
     * Lista documentos del usuario autenticado (filtros opcionales).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = UserDocument::where('user_id', $user->id)
            ->when($request->filled('cita_id'), fn($q) => $q->where('cita_id', $request->integer('cita_id')))
            ->when($request->filled('category'), fn($q) => $q->where('category', $request->string('category')))
            ->orderByDesc('created_at');

        $docs = $query->paginate(15);

        // Devuelvo JSON (si prefieres vista blade, cámbialo a return view(...))
        return response()->json($docs);
    }

    /**
     * (Admin) Lista documentos de un usuario por ID.
     * Protege esta ruta con tu middleware 'admin.auth'.
     */
    public function indexForUser(Request $request, $userId)
    {
        $docs = UserDocument::where('user_id', $userId)
            ->when($request->filled('cita_id'), fn($q) => $q->where('cita_id', $request->integer('cita_id')))
            ->when($request->filled('category'), fn($q) => $q->where('category', $request->string('category')))
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($docs);
    }

    /**
     * Sube un nuevo documento para el usuario autenticado.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'file'        => ['required', 'file', 'max:20480'], // 20MB por ejemplo
            'cita_id'     => ['nullable', 'integer', 'exists:citas,id'],
            'category'    => ['nullable', 'string', 'max:100'],
            'label'       => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'meta'        => ['nullable', 'array'],
            'disk'        => ['nullable', 'string', 'max:50'], // opcional: permitir indicar disk
        ]);

        $disk = $validated['disk'] ?? 'public';
        $file = $validated['file'];

        // Generar path consistente: users/{user_id}/docs/{uuid}.{ext}
        $ext = $file->getClientOriginalExtension();
        $uuid = (string) Str::uuid();
        $baseDir = "users/{$user->id}/docs";
        $filename = $uuid . ($ext ? ('.' . strtolower($ext)) : '');
        $path = "{$baseDir}/{$filename}";

        // Guardar archivo
        Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()));

        // Guardar registro
        $doc = UserDocument::create([
            'user_id'       => $user->id,
            'cita_id'       => $validated['cita_id'] ?? null,
            'disk'          => $disk,
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
            'hash_md5'      => @md5_file($file->getRealPath()) ?: null,
            'category'      => $validated['category'] ?? null,
            'label'         => $validated['label'] ?? null,
            'description'   => $validated['description'] ?? null,
            'meta'          => $validated['meta'] ?? null,
            'is_reviewed'   => false,
        ]);

        return response()->json([
            'success' => true,
            'document' => $doc,
        ], 201);
    }

    /**
     * Descarga el documento (del owner o admin).
     */
    public function download(Request $request, UserDocument $document)
    {
        $this->authorizeDoc($request->user(), $document);

        if (!Storage::disk($document->disk)->exists($document->path)) {
            return response()->json(['message' => 'Archivo no encontrado.'], 404);
        }

        return Storage::disk($document->disk)->download(
            $document->path,
            $document->original_name // nombre sugerido de descarga
        );
    }

    /**
     * Actualiza metadatos del documento y (opcional) reemplaza el archivo.
     */
    public function update(Request $request, UserDocument $document)
    {
        $this->authorizeDoc($request->user(), $document);

        $validated = $request->validate([
            'cita_id'     => ['nullable', 'integer', 'exists:citas,id'],
            'category'    => ['nullable', 'string', 'max:100'],
            'label'       => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'meta'        => ['nullable', 'array'],
            'file'        => ['nullable', 'file', 'max:20480'], // reemplazo de archivo
        ]);

        DB::transaction(function () use ($validated, $document, $request) {
            // Si viene archivo nuevo, primero eliminamos el anterior y subimos el nuevo
            if ($request->hasFile('file')) {
                // Borrar físico del anterior
                if ($document->path) {
                    try { Storage::disk($document->disk)->delete($document->path); } catch (\Throwable $e) {}
                }

                $file = $validated['file'];
                $disk = $document->disk; // mantenemos el mismo disk

                $ext = $file->getClientOriginalExtension();
                $uuid = (string) Str::uuid();
                $baseDir = "users/{$document->user_id}/docs";
                $filename = $uuid . ($ext ? ('.' . strtolower($ext)) : '');
                $path = "{$baseDir}/{$filename}";

                Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()));

                // Actualizar metadatos de archivo
                $document->path          = $path;
                $document->original_name = $file->getClientOriginalName();
                $document->mime_type     = $file->getClientMimeType();
                $document->size          = $file->getSize();
                $document->hash_md5      = @md5_file($file->getRealPath()) ?: null;
            }

            // Actualizar metadatos semánticos
            foreach (['cita_id','category','label','description','meta'] as $attr) {
                if (array_key_exists($attr, $validated)) {
                    $document->{$attr} = $validated[$attr];
                }
            }

            $document->save();
        });

        return response()->json(['success' => true, 'document' => $document->fresh()]);
    }

    /**
     * Elimina el documento (registro + archivo físico por hook del modelo).
     */
    public function destroy(Request $request, UserDocument $document)
    {
        $this->authorizeDoc($request->user(), $document);

        $document->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Autorización básica: dueño del doc o admin.
     * Ajusta esta lógica si tu app tiene roles diferentes.
     */
    private function authorizeDoc(?\App\Models\User $actor, UserDocument $document): void
    {
        if (!$actor) {
            abort(401);
        }

        // Si tiene middleware admin.auth en la ruta, ya vendrá validado como admin.
        // Para robustez, si tienes un flag is_admin en User, podrías usarlo aquí.
        $isAdmin = $actor->can('admin') || $actor->is_admin ?? false;

        if ($document->user_id !== $actor->id && !$isAdmin) {
            abort(403, 'No tienes permiso para acceder a este documento.');
        }
    }
public function indexPage(Request $request)
{
    $user = $request->user();

    $q     = trim((string) $request->get('q', ''));
    $from  = $request->get('from');
    $to    = $request->get('to');

    $docs = \App\Models\UserDocument::where('user_id', $user->id)
        ->when($q !== '', function ($qBuilder) use ($q) {
            $like = '%'.$q.'%';
            $qBuilder->where(function ($w) use ($like) {
                $w->where('original_name', 'LIKE', $like)
                  ->orWhere('label', 'LIKE', $like)
                  ->orWhere('description', 'LIKE', $like)
                  ->orWhere('category', 'LIKE', $like);
            });
        })
        ->when($from, fn($qb) => $qb->whereDate('created_at', '>=', $from))
        ->when($to,   fn($qb) => $qb->whereDate('created_at', '<=', $to))
        ->latest()
        ->paginate(10);

    return view('portal.historial.index', compact('docs'));
}


public function createPage()
{
    // Solo muestra la vista con el componente de subir
    return view('portal.historial.create');
}

}
