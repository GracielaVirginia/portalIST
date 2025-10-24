<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GestionSaludCompleta;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LicenciasController extends Controller
{
    /**
     * RUTA: GET /portal/licencias  (name: portal.licencias.index)
     * VISTA: resources/views/portal/licencias/index.blade.php
     */
    public function index(Request $request)
    {
        // -----------------------------
        // 1) Filtros por query string
        // -----------------------------
        $estado        = strtoupper($request->query('estado', 'TODAS')); // PENDIENTE|CONFIRMADA|RECHAZADA|TODAS
        $from          = $request->query('from'); // YYYY-MM-DD
        $to            = $request->query('to');   // YYYY-MM-DD
        $especialidad  = $request->query('esp');  // RX|LAB|ECO|...
        $origen        = $request->query('org');  // WEB|CALLCENTER|PRESENCIAL|...

        // -----------------------------
        // 2) Identidad del paciente
        // -----------------------------
        $user    = $request->user();
        $tipoDoc = 'RUT';
        $numero  = (!empty($user?->rut)) ? strtoupper((string)$user->rut) : null;

        $applyPatient = function ($q) use ($numero, $user, $tipoDoc) {
            if ($numero) {
                $q->where('tipo_documento', $tipoDoc)
                    ->where('numero_documento', $numero);
            } elseif (!empty($user?->email)) {
                $q->where('email', $user->email);
            }
            return $q;
        };

        // Normalizador de especialidad
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
            if (Str::contains($up, 'RADIO') || Str::contains($up, 'RX'))   return ['code' => 'RX', 'label' => 'Radiografía'];
            if (Str::contains($up, 'ECOG')  || Str::contains($up, 'ECO'))  return ['code' => 'ECO', 'label' => 'Ecografía'];
            if (Str::contains($up, 'LAB'))                                return ['code' => 'LAB', 'label' => 'Laboratorio'];
            if (Str::contains($up, 'ENDO'))                               return ['code' => 'ENDO', 'label' => 'Endocrinología'];
            if (Str::contains($up, 'INTERNA') || Str::contains($up, 'MED')) return ['code' => 'MED_INT', 'label' => 'Medicina Interna'];

            return ['code' => ($up ?: 'OTRO'), 'label' => ($raw ?: 'Otro')];
        };

        // -----------------------------
        // 3) Base del query (filtrada por paciente)
        //    NOTA: Define aquí qué filas son “licencias”.
        //    Si tienes una columna tipo_gestion, úsala; si no, deja el whereNull/NotNull que ya uses.
        // -----------------------------
        $q = $applyPatient(GestionSaludCompleta::query());

        // Ejemplos de criterios. Adapta a tu modelo real:
        // $q->where('tipo_gestion', 'LICENCIA');
        // o si guardas banderas:
        // $q->where('es_licencia', true);
        //
        // Para no romper si aún no tienes esas columnas, lo dejo sin restricción extra.

        // Filtros
        if ($estado && $estado !== 'TODAS') {
            $q->whereRaw('UPPER(estado_solicitud) = ?', [$estado]);
        }
        if ($from) {
            $q->whereDate('fecha_solicitud', '>=', $from);
        }
        if ($to) {
            $q->whereDate('fecha_solicitud', '<=', $to);
        }
        if ($especialidad) {
            // aceptamos MED_INT con guión bajo
            $espInfo = $mapEsp($especialidad);
            $q->where(function ($qq) use ($espInfo) {
                $qq->whereRaw('UPPER(especialidad) = ?', [$espInfo['code']])
                    ->orWhere('especialidad', 'LIKE', '%' . $espInfo['label'] . '%');
            });
        }
        if ($origen) {
            $q->whereRaw('UPPER(origen_solicitud) = ?', [strtoupper($origen)]);
        }

        $q->orderByDesc('fecha_solicitud')->orderByDesc('created_at');

        $rows = $q->get();

        // -----------------------------
        // 4) Header de paciente (para panel-header)
        // -----------------------------
        $gHeader = $rows->first();
        $edad = null;
        if (!empty($gHeader?->fecha_nacimiento)) {
            try {
                $edad = Carbon::parse($gHeader->fecha_nacimiento)->age;
            } catch (\Throwable $e) {
            }
        }
        $paciente = [
            'nombre'      => $gHeader->nombre_paciente ?? ($user->name ?? 'Paciente'),
            'rut'         => $gHeader->numero_documento ?? ($user->rut ?? null),
            'sexo'        => strtoupper((string)($gHeader->sexo ?? '')),
            'edad'        => $edad,
            'idioma'      => strtolower((string)($gHeader->idioma_preferido ?? 'es')),
            'cronico'     => false,
            'condiciones' => [],
        ];

        // -----------------------------
        // 5) Agrupar por especialidad -> $grupos (para la vista con filas de 4)
        // -----------------------------
        $grupos = [];
        foreach ($rows as $r) {
            $esp = $mapEsp($r->especialidad);
            $code  = $esp['code'];
            $label = $esp['label'];

            if (!isset($grupos[$code])) {
                $grupos[$code] = [
                    'code'  => $code,
                    'label' => $label,
                    'items' => collect(),
                ];
            }

            $titulo = $r->licencia_titulo
                ?? $r->examen_nombre
                ?? ('Licencia ' . ($r->examen_codigo ?: '—'));

            $grupos[$code]['items']->push([
                'id'           => $r->id,
                'titulo'       => $titulo,
                'fecha'        => optional($r->fecha_solicitud)->format('Y-m-d H:i') ?: optional($r->created_at)->format('Y-m-d H:i'),
                'codigo'       => $r->examen_codigo ?: ($r->licencia_codigo ?? '—'),
                'estado'       => $r->estado_solicitud ? strtoupper($r->estado_solicitud) : 'PENDIENTE',
                'resumen'      => $r->resumen_atencion ?: ($r->impresion_diagnostica ?: ($r->licencia_resumen ?? '—')),
                'lugar'        => $r->lugar_cita ?: ($r->lugar_emision ?? '—'),
                'profesional'  => $r->id_profesional ?: ($r->profesional_emisor ?? '—'),
                // La vista de licencias abre siempre "licencia-prueba" en modal, así que no pasamos URL PDF aquí.
                // Si más adelante tienes una URL específica por licencia, puedes añadir 'pdf' => $url.
            ]);
        }

        // Ordenar grupos e items
        $grupos = collect($grupos)
            ->sortBy('label')
            ->map(function ($grp) {
                $grp['items'] = $grp['items']->sortByDesc('fecha')->values();
                return $grp;
            })
            ->values();

        // -----------------------------
        // 6) KPIs (sobre la misma base filtrada por paciente)
        // -----------------------------
        // Si prefieres precisión exacta con filtros adicionales (from/to/esp/origen), calcula desde $rows directamente:
        $kpis = [
            'pendientes'  => $rows->where('estado_solicitud', 'PENDIENTE')->count(),
            'confirmadas' => $rows->where('estado_solicitud', 'CONFIRMADA')->count(),
            'rechazadas'  => $rows->where('estado_solicitud', 'RECHAZADA')->count(),
            'vencidas'    => $rows
                ->filter(function ($r) {
                    // Ejemplo de criterio de vencida: >30 días sin fecha_cita_programada
                    $fecha = $r->fecha_solicitud ? Carbon::parse($r->fecha_solicitud) : null;
                    return empty($r->fecha_cita_programada)
                        && $fecha
                        && $fecha->lt(Carbon::today()->subDays(30));
                })->count(),
        ];

        // -----------------------------
        // 7) Meta para la vista (selects)
        // -----------------------------
        $filtros = [
            'estado'       => $estado,
            'from'         => $from,
            'to'           => $to,
            'especialidad' => $especialidad,
            'origen'       => $origen,
        ];

        // catálogos
        $estadosDisponibles = ['PENDIENTE', 'CONFIRMADA', 'RECHAZADA', 'TODAS'];
        // Generamos especialidades disponibles desde los datos del usuario:
        $especialidades = $rows->pluck('especialidad')
            ->filter()
            ->map(fn($e) => $mapEsp($e)['code'])
            ->unique()
            ->values()
            ->all();

        // Orígenes observados en los datos (o defaults si no hay):
        $origenes = $rows->pluck('origen_solicitud')->filter()->unique()->values()->all();
        if (empty($origenes)) {
            $origenes = ['WEB', 'CALLCENTER', 'PRESENCIAL'];
        }

        // -----------------------------
        // 8) Retorno a la vista
        // -----------------------------
        return view('portal.licencias.index', [
            'paciente'           => $paciente,
            'grupos'             => $grupos,         // 👈 lo que pinta la vista por filas (4 cards + modales)
            'kpis'               => $kpis,
            'filtros'            => $filtros,
            'estadosDisponibles' => $estadosDisponibles,
            'especialidades'     => $especialidades,
            'origenes'           => $origenes,
        ]);
    }
}
