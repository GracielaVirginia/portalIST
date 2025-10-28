<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\GestionSaludCompleta;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /* =========================
     * VISTAS
     * ========================= */

    /** Vista base (si la usas) */
    public function index()
    {
        return view('admin.users.index');
    }

    /** Vista: Usuarios registrados (DataTable) */
    public function registered()
    {
        return view('admin.users.registered');
    }

    /** Vista: Usuarios NO registrados (DataTable) */
    public function unregistered()
    {
        return view('admin.users.unregistered');
    }

    /* =========================
     * ENDPOINTS DATATABLES
     * ========================= */

    /** Endpoint DataTables — Usuarios registrados */
    public function registeredData(Request $request)
    {
        try {
            // Parámetros DataTables
            $draw   = (int) $request->input('draw', 1);
            $start  = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $search = trim((string) $request->input('search.value', ''));

            // Columnas visibles (DataTables -> alias del subquery "t")
            $columns = [
                0 => 'id',
                1 => 'rut',
                2 => 'name',
                3 => 'email',
                4 => 'lugar_cita',
                5 => 'created_at',
            ];
            $orderColIdx = (int) $request->input('order.0.column', 0);
            $orderDir    = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
            $orderCol    = $columns[$orderColIdx] ?? 'id';

            // SELECT base (sin filtros) — usamos COALESCE para tener alias "name"
            $base = DB::table('users')->select([
                'id',
                DB::raw("COALESCE(rut, '')           as rut"),
                DB::raw("COALESCE(name, '') as name"),
                DB::raw("COALESCE(email, '')         as email"),
                DB::raw("COALESCE(lugar_cita, '')         as lugar_cita"),
                'created_at',
            ]);

            // Total bruto (sin filtro)
            $recordsTotal = (clone $base)->count();

            // Subquery para poder usar los ALIAS en WHERE/ORDER
            $sub = DB::query()->fromSub($base, 't');

            // Búsqueda
            if ($search !== '') {
                $sub->where(function ($q) use ($search) {
                    $q->where('t.rut', 'like', "%{$search}%")
                      ->orWhere('t.name', 'like', "%{$search}%")
                      ->orWhere('t.email', 'like', "%{$search}%")
                      ->orWhere('t.lugar_cita', 'like', "%{$search}%");
                });
            }

            // Total filtrado
            $recordsFiltered = (clone $sub)->count();

            // Orden + Paginación (sobre alias del subquery)
            $rows = $sub
                ->orderBy("t.$orderCol", $orderDir)
                ->skip($start)
                ->take($length)
                ->get();

            // Mapeo seguro (fechas)
            $data = $rows->map(function ($r) {
                return [
                    'id'         => $r->id,
                    'rut'        => $r->rut,
                    'name'       => $r->name,
                    'email'      => $r->email,
                    'lugar_cita'      => $r->lugar_cita,
                    'created_at' => $this->safeDateTime($r->created_at),
                ];
            });

            return response()->json([
                'draw'            => $draw,
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Throwable $e) {
            Log::error('[DT registeredData] '.$e->getMessage(), [
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Respuesta compatible con DataTables (evita tn/7)
            return response()->json([
                'draw'            => (int) $request->input('draw', 1),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => 'Error al cargar los usuarios registrados.',
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /** Endpoint DataTables — Usuarios NO registrados */
public function unregisteredData(Request $request)
{
    try {
        $draw   = (int) $request->input('draw', 1);
        $start  = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = trim((string) $request->input('search.value', ''));

        // Columnas visibles en DataTables (subquery alias "t")
        $columns = [
            0 => 'numero_documento',
            1 => 'nombre_paciente',
            2 => 'email',
            3 => 'telefono',
            4 => 'dia',
            5 => 'gestiones',
        ];
        $orderColIdx = (int) $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $orderCol    = $columns[$orderColIdx] ?? 'numero_documento';

        // Día clínico: usa fecha_solicitud si existe, si no created_at
        $diaExpr = DB::raw("DATE(COALESCE(g.fecha_solicitud, g.created_at))");

        // Base: pacientes que NO están en users.rut
        // AGRUPADO SOLO por doc + día. Campos “de presentación” con agregados.
        $base = DB::table('gestiones_salud_completa as g')
            ->leftJoin('users as u', 'u.rut', '=', 'g.numero_documento')
            ->whereNull('u.id')
            ->groupBy('g.numero_documento', $diaExpr)
            ->select([
                'g.numero_documento',
            ])
            ->selectRaw('MIN(COALESCE(g.nombre_paciente, "")) as nombre_paciente')
            ->selectRaw('MIN(COALESCE(g.email, ""))           as email')
            ->selectRaw('MIN(COALESCE(g.telefono, ""))        as telefono')
            ->selectRaw('DATE(COALESCE(g.fecha_solicitud, g.created_at)) as dia')
            ->selectRaw('COUNT(*) as gestiones')
            // Detalles de ese día: todas las gestiones concatenadas
            ->selectRaw("
                GROUP_CONCAT(
                    CONCAT(
                        COALESCE(g.id,''), ': ',
                        COALESCE(g.examen_codigo,''), ' — ',
                        COALESCE(g.examen_nombre,'')
                    )
                    ORDER BY COALESCE(g.fecha_solicitud, g.created_at)
                    SEPARATOR ' || '
                ) as detalles_str
            ")
            ->selectRaw('MIN(COALESCE(g.fecha_solicitud, g.created_at)) as primera_fecha');

        // Totales
        $recordsTotal = DB::query()->fromSub($base, 't')->count();

        // Subquery para buscar/ordenar por alias
        $sub = DB::query()->fromSub($base, 't');

        if ($search !== '') {
            $sub->where(function ($q) use ($search) {
                $q->where('t.numero_documento', 'like', "%{$search}%")
                  ->orWhere('t.nombre_paciente', 'like', "%{$search}%")
                  ->orWhere('t.email', 'like', "%{$search}%")
                  ->orWhere('t.telefono', 'like', "%{$search}%")
                  ->orWhere('t.dia', 'like', "%{$search}%")
                  ->orWhere('t.detalles_str', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $sub)->count();

        $rows = $sub
            ->orderBy("t.$orderCol", $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function ($r) {
            $detalles = [];
            if (!empty($r->detalles_str)) {
                $detalles = array_map('trim', explode(' || ', $r->detalles_str));
            }
            return [
                'numero_documento' => $r->numero_documento,
                'nombre_paciente'  => $r->nombre_paciente,
                'email'            => $r->email,
                'telefono'         => $r->telefono,
                'dia'              => $r->dia,
                'gestiones'        => (int) $r->gestiones,
                'detalles'         => $detalles,
                'primera_fecha'    => $this->safeDateTime($r->primera_fecha),
            ];
        });

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE);

    } catch (\Throwable $e) {
        \Log::error('[DT unregisteredData] '.$e->getMessage(), [
            'line'  => $e->getLine(),
            'file'  => $e->getFile(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'draw'            => (int) $request->input('draw', 1),
            'recordsTotal'    => 0,
            'recordsFiltered' => 0,
            'data'            => [],
            'error'           => 'Error al agrupar los usuarios no registrados.',
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}


    /* =========================
     * HELPERS
     * ========================= */

    /**
     * Formatea fecha/hora segura para JSON DataTables.
     * Acepta string|Carbon|null y devuelve 'Y-m-d H:i' o null.
     */
    private function safeDateTime($value): ?string
    {
        if (!$value) return null;

        try {
            // evitar '0000-00-00 ...'
            if (is_string($value) && str_starts_with($value, '0000-00-00')) {
                return null;
            }
            $c = $value instanceof Carbon ? $value : Carbon::parse($value);
            return $c->format('Y-m-d H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }

public function editUnregistered(string $rut)
{
    $paciente = GestionSaludCompleta::porRut($rut)
        ->orderByDesc(DB::raw('COALESCE(fecha_solicitud, created_at)'))
        ->first();

    if (!$paciente) {
        abort(404, 'Paciente no encontrado.');
    }

    $sedes = GestionSaludCompleta::query()
        ->whereNotNull('lugar_cita')
        ->distinct()
        ->orderBy('lugar_cita')
        ->pluck('lugar_cita')
        ->filter()
        ->values();

    return view('admin.users.unregistered_edit', [
        'rut' => $rut,
        'paciente' => $paciente,
        'sedes' => $sedes,
    ]);
}

public function updateUnregistered(Request $request, string $rut)
{
    $validated = $request->validate([
        'nombre_paciente' => 'nullable|string|max:255',
        'email' => 'nullable|string|max:255',
        'telefono' => 'nullable|string|max:100',
        'direccion' => 'nullable|string|max:255',
        'fecha_nacimiento' => 'nullable|date',
        'sexo' => 'nullable|string|max:50',
        'genero' => 'nullable|string|max:50',
        'lugar_cita' => 'nullable|string|max:255',
    ]);

    foreach ($validated as $k => $v) {
        if (is_string($v) && trim($v) === '') {
            $validated[$k] = null;
        }
    }

    $updated = GestionSaludCompleta::porRut($rut)->update($validated);

    if ($updated) {
        return redirect()
            ->route('admin.users.unregistered.edit', $rut)
            ->with('ok', 'Datos actualizados correctamente.');
    }

    return back()->withErrors('No se pudo actualizar el registro.');
}

public function updateUnregisteredEmail(Request $request, string $rut)
{
    $data = $request->validate([
        'email' => ['required','email','max:150'],
    ]);

    // Actualiza TODOS los registros del RUT (consistencia)
    // Si prefieres solo el más reciente, usa ->latest('created_at')->first() y guarda ese.
    $updated = GestionSaludCompleta::porRut($rut)->update([
        'email' => $data['email'],
        'updated_at' => now(), // si tu esquema lo usa
    ]);

    if ($updated === 0) {
        return response()->json(['ok' => false, 'error' => 'No se encontró el RUT.'], 404);
    }

    return response()->json(['ok' => true]);
}

public function updateUnregisteredRut(Request $request, string $rut)
{
    // Tomar el nuevo valor tal cual
    $data = $request->validate([
        'rut' => ['required'],
    ]);

    $newRut = $data['rut'];

    // Actualizar todos los registros del RUT antiguo al nuevo
    $updated = GestionSaludCompleta::porRut($rut)->update([
        'numero_documento' => $newRut,
        'updated_at' => now(),
    ]);

    if ($updated === 0) {
        return response()->json(['ok' => false, 'error' => 'No se encontró el RUT.'], 404);
    }

    return response()->json(['ok' => true]);
}
    public function create()
    {
        // Distincts para selects, ordenados y limpios
        $sexos = GestionSaludCompleta::query()
            ->whereNotNull('sexo')->distinct()->orderBy('sexo')
            ->pluck('sexo')->filter()->values();

        $gruposSang = GestionSaludCompleta::query()
            ->whereNotNull('grupo_sanguineo')->distinct()->orderBy('grupo_sanguineo')
            ->pluck('grupo_sanguineo')->filter()->values();

        $tiposGestion = GestionSaludCompleta::query()
            ->whereNotNull('tipo_gestion')->distinct()->orderBy('tipo_gestion')
            ->pluck('tipo_gestion')->filter()->values();

        $especialidades = GestionSaludCompleta::query()
            ->whereNotNull('especialidad')->distinct()->orderBy('especialidad')
            ->pluck('especialidad')->filter()->values();

        $tiposExamen = GestionSaludCompleta::query()
            ->whereNotNull('tipo_examen')->distinct()->orderBy('tipo_examen')
            ->pluck('tipo_examen')->filter()->values();

        $examenCodigos = GestionSaludCompleta::query()
            ->whereNotNull('examen_codigo')->distinct()->orderBy('examen_codigo')
            ->pluck('examen_codigo')->filter()->values();

        $examenNombres = GestionSaludCompleta::query()
            ->whereNotNull('examen_nombre')->distinct()->orderBy('examen_nombre')
            ->pluck('examen_nombre')->filter()->values();

        $sedes = GestionSaludCompleta::query()
            ->whereNotNull('lugar_cita')->distinct()->orderBy('lugar_cita')
            ->pluck('lugar_cita')->filter()->values();

        $estadosSolicitud = GestionSaludCompleta::query()
            ->whereNotNull('estado_solicitud')->distinct()->orderBy('estado_solicitud')
            ->pluck('estado_solicitud')->filter()->values();

        $tiposAtencion = GestionSaludCompleta::query()
            ->whereNotNull('tipo_atencion')->distinct()->orderBy('tipo_atencion')
            ->pluck('tipo_atencion')->filter()->values();

        $modalidadesAtencion = GestionSaludCompleta::query()
            ->whereNotNull('modalidad_atencion')->distinct()->orderBy('modalidad_atencion')
            ->pluck('modalidad_atencion')->filter()->values();

        $estadosAsistencia = GestionSaludCompleta::query()
            ->whereNotNull('estado_asistencia')->distinct()->orderBy('estado_asistencia')
            ->pluck('estado_asistencia')->filter()->values();

        $nivelesUrgencia = GestionSaludCompleta::query()
            ->whereNotNull('nivel_urgencia')->distinct()->orderBy('nivel_urgencia')
            ->pluck('nivel_urgencia')->filter()->values();

        $idiomas = GestionSaludCompleta::query()
            ->whereNotNull('idioma_preferido')->distinct()->orderBy('idioma_preferido')
            ->pluck('idioma_preferido')->filter()->values();

        $origenesSolicitud = GestionSaludCompleta::query()
            ->whereNotNull('origen_solicitud')->distinct()->orderBy('origen_solicitud')
            ->pluck('origen_solicitud')->filter()->values();

        return view('admin.users.create', compact(
            'sexos','gruposSang','tiposGestion','especialidades','tiposExamen',
            'examenCodigos','examenNombres','sedes','estadosSolicitud',
            'tiposAtencion','modalidadesAtencion','estadosAsistencia',
            'nivelesUrgencia','idiomas','origenesSolicitud'
        ));
    }

    public function store(Request $request)
    {
        // Validación mínima y directa (coincide con tus nombres de columnas)
        $data = $request->validate([
            // Paciente
            'tipo_documento'    => ['required','string','max:20'], // ej: RUT
            'numero_documento'  => ['required','string','max:30'],
            'nombre_paciente'   => ['required','string','max:150'],
            'fecha_nacimiento'  => ['nullable','date'],
            'sexo'              => ['nullable','string','max:30'],
            'genero'            => ['nullable','string','max:50'],
            'telefono'          => ['nullable','string','max:50'],
            'email'             => ['nullable','string','max:150'],
            'direccion'         => ['nullable','string','max:200'],
            'grupo_sanguineo'   => ['nullable','string','max:10'],

            // Preferencias
            'idioma_preferido'      => ['nullable','string','max:50'],
            'notificaciones_email'  => ['nullable','boolean'],
            'notificaciones_sms'    => ['nullable','boolean'],
            'notificaciones_app'    => ['nullable','boolean'],

            // Solicitud / Gestión
            'origen_solicitud'      => ['nullable','string','max:100'],
            'tipo_gestion'          => ['nullable','string','max:100'],
            'especialidad'          => ['nullable','string','max:120'],
            'tipo_examen'           => ['nullable','string','max:120'],
            'examen_codigo'         => ['nullable','string','max:50'],
            'examen_nombre'         => ['nullable','string','max:200'],
            'fecha_solicitud'       => ['nullable','date'],
            'fecha_cita_programada' => ['nullable','date'],
            'lugar_cita'            => ['nullable','string','max:150'],
            'estado_solicitud'      => ['nullable','string','max:100'],

            // Atención
            'tipo_atencion'      => ['nullable','string','max:100'],
            'modalidad_atencion' => ['nullable','string','max:100'],
            'fecha_atencion'     => ['nullable','date'],
            'estado_asistencia'  => ['nullable','string','max:100'],

            // Seguridad
            'nivel_urgencia'     => ['nullable','string','max:50'],
        ]);

        // Normaliza los checkboxes booleanos (si no vienen, false)
        foreach (['notificaciones_email','notificaciones_sms','notificaciones_app'] as $bool) {
            $data[$bool] = isset($data[$bool]) ? (bool)$data[$bool] : false;
        }

        // Auditoría básica
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // Crea el registro
        GestionSaludCompleta::create($data);

        return redirect()
            ->route('admin.users.unregistered')
            ->with('ok', 'Paciente creado correctamente.');
    }
}


