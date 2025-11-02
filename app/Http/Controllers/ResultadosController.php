<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GestionSaludCompleta;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class ResultadosController extends Controller
{
    // ------------------------------------------------------------
    // GET /ver-resultados
    // Vista general: listado y (opcional) totales por especialidad
    // ------------------------------------------------------------

public function index(Request $request)
{
    $user    = $request->user(); // o Auth::user()
    $tipoDoc = 'RUT';
    $numero  = (!empty($user?->rut)) ? strtoupper((string)$user->rut) : null; // RUT desde perfil

    // Filtro por paciente (RUT o email)
    $applyPatient = function ($q) use ($numero, $user, $tipoDoc) {
        if ($numero) {
            $q->where('tipo_documento', $tipoDoc)
              ->where('numero_documento', $numero);
        } elseif (!empty($user?->email)) {
            $q->where('email', $user->email);
        }
        return $q;
    };

    // Normalizador de especialidades
    $mapEsp = function (?string $s): array {
        $raw = trim((string)$s);
        $up  = Str::upper(Str::ascii($raw));

        if (in_array($up, ['RX', 'ECO', 'LAB', 'ENDO', 'MED_INT'], true)) {
            return match ($up) {
                'RX'      => ['code' => 'RX', 'label' => 'Radiografía'],
                'ECO'     => ['code' => 'ECO', 'label' => 'Ecografía'],
                'LAB'     => ['code' => 'LAB', 'label' => 'Laboratorio'],
                'ENDO'    => ['code' => 'ENDO', 'label' => 'Endocrinología'],
                'MED_INT' => ['code' => 'MED_INT', 'label' => 'Medicina Interna'],
            };
        }

        if (Str::contains($up, 'RADIO') || Str::contains($up, 'RX'))
            return ['code' => 'RX', 'label' => 'Radiografía'];
        if (Str::contains($up, 'ECOG') || Str::contains($up, 'ECO'))
            return ['code' => 'ECO', 'label' => 'Ecografía'];
        if (Str::contains($up, 'LAB'))
            return ['code' => 'LAB', 'label' => 'Laboratorio'];
        if (Str::contains($up, 'ENDO'))
            return ['code' => 'ENDO', 'label' => 'Endocrinología'];
        if (Str::contains($up, 'INTERNA') || Str::contains($up, 'MED'))
            return ['code' => 'MED_INT', 'label' => 'Medicina Interna'];

        $label = $raw ?: 'Otro';
        return ['code' => $up ?: 'OTRO', 'label' => $label];
    };

    // Trae todas las gestiones del paciente (ordenadas)
    $gestionesAll = $applyPatient(GestionSaludCompleta::query())
        ->orderByDesc('fecha_atencion')
        ->orderByDesc('created_at')
        ->get();

    // ⚠️ RUT efectivo para el visor: del usuario o, si falta, del primer registro clínico
    $rutFromGestion = strtoupper((string) optional($gestionesAll->first())->numero_documento) ?: null;
    $rutPaciente    = $numero ?: $rutFromGestion ?: null;

    // --- DEBUG base ---
    $debug = [
        'usuario' => [
            'id'    => $user?->id,
            'rut'   => $numero,
            'email' => $user?->email,
        ],
        'rut_from_gestion_first' => $rutFromGestion,
        'rutPaciente_final'      => $rutPaciente,
        'total_gestiones'        => $gestionesAll->count(),
        'sample'                 => [],   // muestreo de ítems
    ];

    // Agrupa por especialidad normalizada -> estructura para la vista
    $grupos = [];
    foreach ($gestionesAll as $idx => $g) {
        $esp   = $mapEsp($g->especialidad);
        $code  = $esp['code'];   // RX, ECO, LAB, ...
        $label = $esp['label'];

        if (!isset($grupos[$code])) {
            $grupos[$code] = [
                'code'  => $code,
                'label' => $label,
                'items' => collect(),
            ];
        }

        // Resolver URL PDF (si es relativo, lo pasamos a asset('informes/...'))
        $pdf = $g->url_pdf_informe;
        $pdfUrl = $pdf
            ? (Str::startsWith($pdf, ['http://', 'https://', '/']) ? $pdf : asset('informes/' . $pdf))
            : null;

        // (Opcional) número de caso si tu tabla lo tiene con otro nombre
        $numeroCaso = $g->numero_caso ?? $g->n_caso ?? $g->caso ?? null;

        // Solo RX/ECO tienen link al visor
        $isRxEco = in_array($code, ['RX','ECO'], true);

        // URL del visor SOLO para RX/ECO y si tenemos RUT
        $viewerUrl = null;
        if ($isRxEco && !empty($rutPaciente)) {
            $viewerUrl = 'https://visor.ist.cl:8085/?patient=' . urlencode($rutPaciente);
            // Si quieres agregar número de caso, descomenta:
            // if (!empty($numeroCaso)) {
            //     $viewerUrl .= '&acc=' . urlencode($numeroCaso);
            // }
        }

        $item = [
            'id'            => $g->id,
            'titulo'        => $g->examen_nombre ?: $label,
            'fecha'         => optional($g->fecha_atencion)->format('Y-m-d H:i') ?: optional($g->created_at)->format('Y-m-d H:i'),
            'codigo'        => $g->examen_codigo ?: '—',
            'estado'        => $g->tiene_informe ? 'DISPONIBLE' : ($g->estado_solicitud ?: '—'),
            'pdf'           => $pdfUrl,
            'lugar'         => $g->lugar_cita ?: '—',
            'profesional'   => $g->id_profesional ?: '—',

            // claves nuevas para la vista
            'is_rx_eco'     => $isRxEco,
            'viewer_url'    => $viewerUrl,
            // para inspección rápida si quieres @dump($item) en Blade:
            'esp_code'      => $code,
            'esp_raw'       => (string)$g->especialidad,
        ];

        $grupos[$code]['items']->push($item);

        // --- DEBUG: muestreo de los primeros 12 ítems ---
        if ($idx < 12) {
            $debug['sample'][] = [
                'id'         => $g->id,
                'esp_raw'    => (string)$g->especialidad,
                'esp_code'   => $code,
                'is_rx_eco'  => $isRxEco,
                'viewer_url' => $viewerUrl,
                'pdf'        => $pdfUrl,
            ];
        }
    }

    // Ordena grupos por label y items por fecha (desc)
    $grupos = collect($grupos)
        ->sortBy('label')
        ->map(function ($grp) {
            $grp['items'] = $grp['items']->sortByDesc('fecha')->values();
            return $grp;
        })
        ->values();

    // Header paciente (como ya lo tenías)
    $gHeader = $gestionesAll->first();
    $paciente = [
        'nombre'      => $gHeader->nombre_paciente ?? ($user->name ?? 'Paciente'),
        'rut'         => $gHeader->numero_documento ?? ($user->rut ?? null),
        'sexo'        => strtoupper((string)($gHeader->sexo ?? '')),
        'edad'        => !empty($gHeader?->fecha_nacimiento) ? optional(Carbon::parse($gHeader->fecha_nacimiento))->age : null,
        'idioma'      => strtolower((string)($gHeader->idioma_preferido ?? 'es')),
        'cronico'     => false,
        'condiciones' => [],
    ];

    // --- DEBUG: enviar a logs ---
    try {
        \Log::debug('[ResultadosController@index] ver-resultados debug', $debug);
    } catch (\Throwable $e) {
        // evitar romper la vista si el log falla
    }

    return view('ver-resultados', [
        'paciente' => $paciente,
        'grupos'   => $grupos, // ← lo que pinta la vista por filas de 4
        'debug'    => $debug,  // ← por si quieres @dump($debug) en la vista
    ]);
}


    // ----------------------------------------------------------------
    // GET /portal/resultados/especialidad/{esp}  (si decides usarla)
    // Lista filtrada por especialidad. Si hay 1 solo, podrías redirigir
    // al detalle (opcional).
    // ----------------------------------------------------------------

public function porEspecialidad(Request $request, string $esp)
{
    $user    = $request->user(); // o Auth::user()
    $tipoDoc = 'RUT';
    $numero  = (!empty($user?->rut)) ? strtoupper((string)$user->rut) : null; // RUT paciente (para visor)

    // Filtro por paciente (RUT o email)
    $applyPatient = function ($q) use ($numero, $user, $tipoDoc) {
        if ($numero) {
            $q->where('tipo_documento', $tipoDoc)
              ->where('numero_documento', $numero);
        } elseif (!empty($user?->email)) {
            $q->where('email', $user->email);
        }
        return $q;
    };

    // Normalizador de especialidad y términos LIKE (sin UNACCENT)
    $mapEsp = function (?string $s): array {
        $raw = trim((string)$s);
        $up  = Str::upper(Str::ascii($raw));

        if (in_array($up, ['RX', 'ECO', 'LAB', 'ENDO', 'MED_INT'], true)) {
            return match ($up) {
                'RX'      => ['code' => 'RX', 'label' => 'Radiografía',       'terms' => ['RX', 'RADIO', 'RADIOGRAFIA', 'RADIOGRAFÍA']],
                'ECO'     => ['code' => 'ECO', 'label' => 'Ecografía',         'terms' => ['ECO', 'ECOG', 'ECOGRAFIA', 'ECOGRAFÍA']],
                'LAB'     => ['code' => 'LAB', 'label' => 'Laboratorio',       'terms' => ['LAB', 'LABORATORIO']],
                'ENDO'    => ['code' => 'ENDO','label' => 'Endocrinología',    'terms' => ['ENDO', 'ENDOCRINOLOGIA', 'ENDOCRINOLOGÍA']],
                'MED_INT' => ['code' => 'MED_INT','label' => 'Medicina Interna','terms' => ['MED', 'INTERNA', 'MEDICINA INTERNA']],
            };
        }

        if (Str::contains($up, 'RADIO') || Str::contains($up, 'RX'))
            return ['code' => 'RX', 'label' => 'Radiografía', 'terms' => ['RX', 'RADIO', 'RADIOGRAFIA', 'RADIOGRAFÍA']];
        if (Str::contains($up, 'ECOG')  || Str::contains($up, 'ECO'))
            return ['code' => 'ECO', 'label' => 'Ecografía',  'terms' => ['ECO', 'ECOG', 'ECOGRAFIA', 'ECOGRAFÍA']];
        if (Str::contains($up, 'LAB'))
            return ['code' => 'LAB', 'label' => 'Laboratorio','terms' => ['LAB', 'LABORATORIO']];
        if (Str::contains($up, 'ENDO'))
            return ['code' => 'ENDO','label' => 'Endocrinología','terms' => ['ENDO', 'ENDOCRINOLOGIA', 'ENDOCRINOLOGÍA']];
        if (Str::contains($up, 'INTERNA') || Str::contains($up, 'MED'))
            return ['code' => 'MED_INT', 'label' => 'Medicina Interna', 'terms' => ['MED', 'INTERNA', 'MEDICINA INTERNA']];

        $ascii = Str::upper(Str::ascii($raw));
        return ['code' => $up ?: 'OTRO', 'label' => $raw ?: 'Otro', 'terms' => array_values(array_unique(array_filter([$raw, $ascii])))];
    };

    $info  = $mapEsp($esp);
    $terms = collect($info['terms'])->map(fn($t) => trim((string)$t))->filter()->unique()->values()->all();

    // ---------- Header (paciente) ----------
    $gHeader = $applyPatient(\App\Models\GestionSaludCompleta::query())
        ->orderByDesc('created_at')
        ->first();

    $nombre = $gHeader->nombre_paciente ?? ($user->name ?? 'Paciente');
    $rut    = $gHeader->numero_documento ?? ($user->rut ?? null); // lo usamos también para visor
    $sexo   = strtoupper((string)($gHeader->sexo ?? ''));
    $idioma = strtolower((string)($gHeader->idioma_preferido ?? 'es'));

    $edad = null;
    if (!empty($gHeader?->fecha_nacimiento)) {
        try {
            $edad = Carbon::parse($gHeader->fecha_nacimiento)->age;
        } catch (\Throwable $e) {}
    }

    $condiciones = [];
    if ($gHeader) {
        if ($gHeader->tiene_hta)      $condiciones[] = 'Hipertenso';
        if ($gHeader->tiene_diabetes) $condiciones[] = 'Diabetes';
        if ($gHeader->tiene_asma)     $condiciones[] = 'Asma';
        if ($gHeader->tiene_erc)      $condiciones[] = 'ERC';
    }

    $paciente = [
        'nombre'      => $nombre,
        'rut'         => $rut,
        'sexo'        => $sexo,
        'edad'        => $edad,
        'idioma'      => $idioma,
        'cronico'     => !empty($condiciones),
        'condiciones' => $condiciones,
    ];

    // ---------- Todas las gestiones del paciente (para sidebar/KPIs/widgets) ----------
    $gestionesAll = $applyPatient(\App\Models\GestionSaludCompleta::query())
        ->orderByDesc('fecha_atencion')
        ->orderByDesc('created_at')
        ->get();

    $totalResultados = $gestionesAll->count();

    // Sidebar: agrupado por especialidad normalizada
    $porEspecialidad = $gestionesAll
        ->groupBy(fn($row) => $mapEsp($row->especialidad)['code'])
        ->map(function ($group, $code) use ($mapEsp) {
            $label = $mapEsp($group->first()->especialidad)['label'];
            return ['especialidad' => $code, 'label' => $label, 'count' => $group->count()];
        })
        ->values()
        ->sortBy('label')
        ->all();

    $sidebar = [
        'resultados' => [
            'total'            => $totalResultados,
            'por_especialidad' => $porEspecialidad,
        ],
    ];

    // KPIs globales (como Home)
    $hoy = Carbon::today();
    $kpis = [
        'proximas_citas'         => $applyPatient(\App\Models\GestionSaludCompleta::query())
            ->whereDate('fecha_cita_programada', '>=', $hoy)
            ->count(),
        'resultados_disponibles' => $totalResultados,
        'ordenes'                => $applyPatient(\App\Models\GestionSaludCompleta::query())
            ->whereNotNull('fecha_solicitud')->count(),
        'alertas'                => $applyPatient(\App\Models\GestionSaludCompleta::query())
            ->where(function ($q) {
                $q->where('seguimiento_requerido', true)
                  ->orWhereDate('fecha_proximo_control', '<=', Carbon::today()->addDays(7));
            })->count(),
    ];

    // Últimos resultados (para modal)
    $itemsRecientes = $gestionesAll->take(6)->map(function ($r) use ($mapEsp) {
        $espN = $mapEsp($r->especialidad);
        return [
            'id'               => $r->id,
            'especialidad'     => $espN['code'],
            'examen_nombre'    => $r->examen_nombre ?: $espN['label'],
            'examen_codigo'    => $r->examen_codigo ?: '',
            'fecha'            => optional($r->fecha_atencion)->format('Y-m-d H:i') ?: optional($r->created_at)->format('Y-m-d H:i'),
            'estado'           => $r->tiene_informe ? 'DISPONIBLE' : ($r->estado_solicitud ?: '—'),
            'url_pdf_informe'  => $r->url_pdf_informe,
            'viewer'           => false,
        ];
    })->values()->all();

    // ---------- Gestiones filtradas por la especialidad solicitada ----------
    $q = $applyPatient(\App\Models\GestionSaludCompleta::query());

    $q->where(function ($qq) use ($terms) {
        foreach ($terms as $t) {
            $qq->orWhere('especialidad', 'LIKE', '%' . $t . '%');
            // $qq->orWhereRaw('especialidad COLLATE utf8mb4_0900_ai_ci LIKE ?', ['%'.$t.'%']); // si tu MySQL lo soporta
        }
    });

    $gestiones = $q->orderByDesc('fecha_atencion')
        ->orderByDesc('created_at')
        ->get();

    // ✳️ Enriquecemos cada gestión con flags/URLs para la vista (sin cambiar columnas de DB)
    $gestiones = $gestiones->map(function ($g) use ($mapEsp, $rut) {
        $espN     = $mapEsp($g->especialidad);
        $code     = $espN['code']; // RX/ECO/LAB/...
        $isRxEco  = in_array($code, ['RX','ECO'], true);

        // Número de caso si existe (ajusta el nombre real del campo)
        $numeroCaso = $g->numero_caso ?? $g->n_caso ?? $g->caso ?? null;

        // Resuelve PDF relativo si lo usas así
        $pdf = $g->url_pdf_informe;
        $pdfUrl = $pdf
            ? (Str::startsWith($pdf, ['http://','https://','/']) ? $pdf : asset('informes/'.$pdf))
            : null;

        // URL visor (solo RX/ECO + RUT)
        $viewerUrl = null;
        if ($isRxEco && !empty($rut)) {
            $viewerUrl = 'https://visor.ist.cl:8085/?patient=' . urlencode($rut);
            //si le quiero agregar eol numero de caso al la url del visor
            // if (!empty($numeroCaso)) {
            //     $viewerUrl .= '&acc=' . urlencode($numeroCaso); // opcional
            // }
        }

        // Atributos "virtuales" para la vista:
        $g->setAttribute('is_rx_eco',   $isRxEco);
        $g->setAttribute('viewer_url',  $viewerUrl);
        $g->setAttribute('pdf_resolved',$pdfUrl);

        return $g;
    });

    // (Opcional) series/sugerencias
    $seriesControles = ['tension' => [], 'glucosa' => [], 'peso' => []];
    $sugerencias     = [];

    return view('ver-resultados-especialidad', [
        'paciente'        => $paciente,
        'kpis'            => $kpis,
        'sidebar'         => $sidebar,
        'itemsRecientes'  => $itemsRecientes,
        'seriesControles' => $seriesControles,
        'sugerencias'     => $sugerencias,

        // 👇 la vista recibe modelos con viewer_url / is_rx_eco listos
        'gestiones'       => $gestiones,
        'esp'             => $info['code'],
    ]);
}



    // ------------------------------------------------------------
    // GET /portal/resultados/{gestion}
    // Detalle de 1 resultado (card con acciones)
    // ------------------------------------------------------------
    public function show(Request $request, int $gestionId)
    {
        // TODO: filtrar por paciente (no exponer datos de otros)
        // $rut = strtoupper($request->user()->rut ?? '');
        // $tipoDoc = 'RUT';

        // $gestion = GestionSaludCompleta::query()
        //     ->when($rut, fn($q) => $q->where('tipo_documento', $tipoDoc)->where('numero_documento', $rut))
        //     ->findOrFail($gestionId);

        // Placeholder:
        $gestion = null;

        // Vista sugerida: resources/views/ver-resultado.blade.php
        return view('ver-resultado', [
            'gestion' => $gestion,
        ]);
    }

    // ------------------------------------------------------------
    // GET /portal/resultados/{gestion}/pdf
    // Descarga o visualización del PDF del informe
    // ------------------------------------------------------------
    public function pdf(Request $request, int $gestionId)
    {
        // $rut = strtoupper($request->user()->rut ?? '');
        // $tipoDoc = 'RUT';

        // $gestion = GestionSaludCompleta::query()
        //     ->when($rut, fn($q) => $q->where('tipo_documento', $tipoDoc)->where('numero_documento', $rut))
        //     ->findOrFail($gestionId);

        // if (empty($gestion->url_pdf_informe)) {
        //     abort(404, 'El informe PDF no está disponible.');
        // }

        // Si guardas en storage local/public:
        // return Storage::disk('public')->download($gestion->url_pdf_informe);

        // Si es URL absoluta (S3/externo), puedes redirigir:
        // return redirect()->away($gestion->url_pdf_informe);

        abort(404, 'PDF no implementado (MVP).');
    }

    // ------------------------------------------------------------
    // GET /portal/resultados/{gestion}/viewer
    // Enviar al viewer PACS o embebido (si aplica)
    // ------------------------------------------------------------
    public function viewer(Request $request, int $gestionId)
    {
        // TODO: recuperar link al viewer si tuvieras un campo tipo url_pacs_viewer
        // $rut = strtoupper($request->user()->rut ?? '');
        // $tipoDoc = 'RUT';

        // $gestion = GestionSaludCompleta::query()
        //     ->when($rut, fn($q) => $q->where('tipo_documento', $tipoDoc)->where('numero_documento', $rut))
        //     ->findOrFail($gestionId);

        // if (!$gestion->url_pacs_viewer) {
        //     abort(404, 'Viewer no disponible para este estudio.');
        // }

        // return redirect()->away($gestion->url_pacs_viewer);

        abort(404, 'Viewer no implementado (MVP).');
    }
}
