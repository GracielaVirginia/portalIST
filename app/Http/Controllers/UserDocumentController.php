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
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
class UserDocumentController extends Controller
{

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
    public function indexForUser(Request $request, $userId)
    {
        $docs = UserDocument::where('user_id', $userId)
            ->when($request->filled('cita_id'), fn($q) => $q->where('cita_id', $request->integer('cita_id')))
            ->when($request->filled('category'), fn($q) => $q->where('category', $request->string('category')))
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($docs);
    }
public function store(Request $request)
{
    $user = $request->user();

    // Tipos permitidos (ajusta a tu necesidad)
    $extAllowed  = ['pdf','jpg','jpeg','png','webp','doc','docx','xls','xlsx','csv'];
    $mimeAllowed = [
        'application/pdf',
        'image/jpeg','image/png','image/webp',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
    ];

    try {
        // Validación (max está en KB → 20480 = 20MB)
        $validated = $request->validate([
            'file'        => ['required','file','max:20480',"mimes:".implode(',',$extAllowed),"mimetypes:".implode(',',$mimeAllowed)],
            'cita_id'     => ['nullable','integer','exists:citas,id'],
            'category'    => ['nullable','string','max:100'],
            'label'       => ['nullable','string','max:150'],
            'description' => ['nullable','string'],
            'meta'        => ['nullable','array'],
            'disk'        => ['nullable','string','max:50'],
        ]);
    } catch (ValidationException $e) {
        // Mensaje más claro para usuarios
        $msg = 'El archivo no cumple los requisitos.';
        if ($e->validator->errors()->has('file')) {
            $err = $e->validator->errors()->first('file');
            if (str_contains($err, 'max'))   { $msg = 'El archivo supera el tamaño máximo permitido (20 MB).'; }
            if (str_contains($err, 'mimes')) { $msg = 'Tipo de archivo no permitido. Sube PDF, imagen o documento de Office.'; }
        }

        if ($request->expectsJson()) {
            return response()->json(['ok'=>false,'reason'=>'validation','errors'=>$e->errors(),'message'=>$msg], 422);
        }
        return back()->withErrors($e->errors())->with('error', $msg)->withInput();
    }

    $disk = $validated['disk'] ?? 'public';
    $file = $validated['file'];

    try {
        // Directorio y nombre consistente
        $ext      = strtolower($file->getClientOriginalExtension() ?: '');
        $uuid     = (string) Str::uuid();
        $baseDir  = "users/{$user->id}/docs";
        $filename = $ext ? "{$uuid}.{$ext}" : $uuid;

        // Guarda de forma eficiente (streaming)
        // Equivalente a put($path, file_get_contents(...)) pero más seguro/performante
        $storedPath = Storage::disk($disk)->putFileAs($baseDir, $file, $filename);

        // Metadatos
        $original  = $file->getClientOriginalName();
        $mime      = $file->getClientMimeType();
        $size      = $file->getSize();
        $hash      = @md5_file($file->getRealPath()) ?: null;

        // Registro en BD
        $doc = \App\Models\UserDocument::create([
            'user_id'       => $user->id,
            'cita_id'       => $validated['cita_id'] ?? null,
            'disk'          => $disk,
            'path'          => $storedPath, // p.ej. users/{id}/docs/{uuid}.pdf
            'original_name' => $original,
            'mime_type'     => $mime,
            'size'          => $size,
            'hash_md5'      => $hash,
            'category'      => $validated['category'] ?? null,
            'label'         => $validated['label'] ?? null,
            'description'   => $validated['description'] ?? null,
            'meta'          => $validated['meta'] ?? null,
            'is_reviewed'   => false,
        ]);

return response()->json([
    'ok'           => true,
    'message'      => 'Documento subido correctamente.',
    'document'     => $doc,
    'redirect_url' => route('portal.historial.index'), // <- clave para redirigir desde fetch
], 201);


        return back()->with('success', 'Documento subido correctamente.');

    } catch (QueryException $e) {
        if ($request->expectsJson()) {
            return response()->json(['ok'=>false,'reason'=>'db','message'=>'No pudimos guardar el registro del documento.'], 500);
        }
        return back()->with('error', 'No pudimos guardar el registro del documento.')->withInput();
    } catch (\Throwable $e) {
        // Fallo de disco, permisos, etc.
        if ($request->expectsJson()) {
            return response()->json(['ok'=>false,'reason'=>'unexpected','message'=>'Ocurrió un error al subir el archivo.'], 500);
        }
        return back()->with('error', 'Ocurrió un error al subir el archivo.')->withInput();
    }
}
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

    public function destroy(Request $request, UserDocument $document)
    {
        $this->authorizeDoc($request->user(), $document);

        $document->delete();

    return redirect()
        ->route('portal.historial.index')
        ->with('success', 'Archivo eliminado correctamente.');    }
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
    return view('portal.historial.create');
}

}
