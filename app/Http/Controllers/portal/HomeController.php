<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GestionSaludCompleta;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Noticia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1) Identificador del paciente
        $tipoDoc = 'RUT';
        $numero  = null;
        if ($user && isset($user->rut) && $user->rut) {
            $numero = strtoupper($user->rut);
        }

        // Helper para aplicar el filtro del paciente
        $applyPatient = function ($q) use ($numero, $user, $tipoDoc) {
            if ($numero) {
                $q->where('tipo_documento', $tipoDoc)
                  ->where('numero_documento', $numero);
            } elseif ($user && isset($user->email) && $user->email) {
                $q->where('email', $user->email);
            }
            return $q;
        };

        // 2) Registro más reciente del paciente para poblar header
        $g = $applyPatient(GestionSaludCompleta::query())
            ->orderByDesc('created_at')
            ->first();

        $nombre = $g->nombre_paciente ?? ($user->name ?? 'Paciente');
        $rut    = $g->numero_documento ?? ($user->rut ?? null);
        $sexo   = strtoupper((string)($g->sexo ?? ''));
        $idioma = strtolower((string)($g->idioma_preferido ?? 'es'));

        $edad = null;
        if (!empty($g?->fecha_nacimiento)) {
            try {
                $edad = Carbon::parse($g->fecha_nacimiento)->age;
            } catch (\Throwable $e) {}
        }

        // Flags crónicos
        $condiciones = [];
        $cronico = false;
        if ($g) {
            if ($g->tiene_hta)      $condiciones[] = 'Hipertenso';
            if ($g->tiene_diabetes) $condiciones[] = 'Diabetes';
            if ($g->tiene_asma)     $condiciones[] = 'Asma';
            if ($g->tiene_erc)      $condiciones[] = 'ERC';
            $cronico = !empty($condiciones);
        }

        $paciente = [
            'nombre'      => $nombre,
            'rut'         => $rut,
            'sexo'        => $sexo,
            'edad'        => $edad,
            'idioma'      => $idioma,
            'cronico'     => $cronico,
            'condiciones' => $condiciones,
        ];

        // === Normalizador de especialidades -> códigos consistentes para los componentes
        $mapEsp = function (?string $s): array {
            $raw = trim((string)$s);
            $up  = Str::upper(Str::ascii($raw));
            if (Str::contains($up, 'RADIO') || Str::contains($up, 'RX')) {
                return ['code' => 'RX', 'label' => 'Radiografía'];
            }
            if (Str::contains($up, 'ECOG') || Str::contains($up, 'ECO')) {
                return ['code' => 'ECO', 'label' => 'Ecografía'];
            }
            if (Str::contains($up, 'LAB')) {
                return ['code' => 'LAB', 'label' => 'Laboratorio'];
            }
            if (Str::contains($up, 'ENDO')) {
                return ['code' => 'ENDO', 'label' => 'Endocrinología'];
            }
            if (Str::contains($up, 'MED') || Str::contains($up, 'INTERNA')) {
                return ['code' => 'MED_INT', 'label' => 'Medicina Interna'];
            }
            return ['code' => $up ?: 'OTRO', 'label' => ($raw ?: 'Otro')];
        };

        // 3) Traemos TODAS las gestiones del paciente (lo que llamas “resultados”)
        $gestiones = $applyPatient(GestionSaludCompleta::query())
            ->orderByDesc('fecha_atencion')
            ->orderByDesc('created_at')
            ->get();

        $totalResultados = $gestiones->count();

        // 4) Agrupado por especialidad (sidebar)
        $porEspecialidad = $gestiones
            ->groupBy(function ($row) use ($mapEsp) {
                return $mapEsp($row->especialidad)['code'];
            })
            ->map(function ($group, $code) use ($mapEsp) {
                $label = $mapEsp($group->first()->especialidad)['label'];
                return ['especialidad' => $code, 'label' => $label, 'count' => $group->count()];
            })
            ->values()
            ->sortBy('label')
            ->all();

        // 5) KPIs reales (mínimos)
        $hoy = Carbon::today();
        $kpis = [
            'proximas_citas'         => $applyPatient(GestionSaludCompleta::query())
                ->whereDate('fecha_cita_programada', '>=', $hoy)
                ->count(),
            'resultados_disponibles' => $totalResultados,
            'ordenes'                => $applyPatient(GestionSaludCompleta::query())
                ->whereNotNull('fecha_solicitud')
                ->count(),
            'alertas'                => $applyPatient(GestionSaludCompleta::query())
                ->where(function ($q) {
                    $q->where('seguimiento_requerido', true)
                      ->orWhereDate('fecha_proximo_control', '<=', Carbon::today()->addDays(7));
                })->count(),
        ];

        // 6) Widget: últimos resultados
        $itemsRecientes = $gestiones->take(6)->map(function ($r) use ($mapEsp) {
            $esp = $mapEsp($r->especialidad);
            return [
                'id'              => $r->id,
                'especialidad'    => $esp['code'],
                'examen_nombre'   => $r->examen_nombre ?: $esp['label'],
                'examen_codigo'   => $r->examen_codigo ?: '',
                'fecha'           => optional($r->fecha_atencion)->format('Y-m-d H:i') ?: optional($r->created_at)->format('Y-m-d H:i'),
                'estado'          => $r->tiene_informe ? 'DISPONIBLE' : ($r->estado_solicitud ?: '—'),
                'url_pdf_informe' => $r->url_pdf_informe,
                'viewer'          => false,
            ];
        })->values()->all();

        // 7) Sidebar payload
        $sidebar = [
            'resultados' => [
                'total'            => $totalResultados,
                'por_especialidad' => $porEspecialidad,
            ],
        ];

        // 8) Series/sugerencias (placeholder)
        $seriesControles = ['tension' => [], 'glucosa' => [], 'peso' => []];
        $sugerencias     = [];

        // 9) Noticia destacada para el home
        $destacada = Noticia::where('destacada', true)->latest()->first();
        $noticia = $destacada ? [
            'id'     => $destacada->id,
            'titulo' => $destacada->titulo,
            'bajada' => $destacada->bajada,
            'imagen' => $destacada->imagen_url, // accessor del modelo
            'url'    => route('portal.noticias.show', $destacada->id),
        ] : null;

        return view('portal.home', [
            'paciente'         => $paciente,
            'kpis'             => $kpis,
            'sidebar'          => $sidebar,
            'itemsRecientes'   => $itemsRecientes,
            'seriesControles'  => $seriesControles,
            'sugerencias'      => $sugerencias,
            'noticia'          => $noticia,
        ]);
    }
}
