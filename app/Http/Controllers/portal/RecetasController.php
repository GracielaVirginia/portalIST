<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GestionSaludCompleta;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RecetasController extends Controller
{
    /**
     * RUTA: GET /portal/recetas  (name: portal.recetas.index)
     * VISTA: resources/views/portal/recetas/index.blade.php
     */
    public function index(Request $request)
    {
        // -----------------------------
        // 1) Filtros (query params)
        // -----------------------------
        // estado: CON_RECETA | SIN_RECETA | TODAS
        $estado       = strtoupper($request->query('estado', 'CON_RECETA'));
        $from         = $request->query('from'); // YYYY-MM-DD
        $to           = $request->query('to');   // YYYY-MM-DD
        $qText        = trim((string) $request->query('q', '')); // búsqueda libre
        $especialidad = $request->query('esp');  // RX|LAB|ECO|MED_INT|...

        // -----------------------------
        // 2) Identidad del paciente (RUT o email)
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

        // Normalizador de especialidad (coherente con otras vistas)
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
        //    Define aquí qué consideras “recetas”: columnas tiene_receta/url_pdf_receta
        // -----------------------------
        $q = $applyPatient(GestionSaludCompleta::query());

        // Criterio de RECETA (descomenta/adapta según tu esquema real)
        // if ($estado === 'CON_RECETA' || $estado === 'TODAS') {
        //     $q->where(function ($w) {
        //         $w->where('tiene_receta', true)
        //           ->orWhereNotNull('url_pdf_receta');
        //     });
        // } elseif ($estado === 'SIN_RECETA') {
        //     $q->where(function ($w) {
        //         $w->where(function ($x) {
        //             $x->where('tiene_receta', false)->orWhereNull('tiene_receta');
        //         })->whereNull('url_pdf_receta');
        //     });
        // }

        // Estado adicional (si lo manejas en otra columna), si no, omite
        if ($estado === 'CON_RECETA') {
            $q->where(function ($w) {
                $w->where('tiene_receta', true)
                    ->orWhereNotNull('url_pdf_receta');
            });
        } elseif ($estado === 'SIN_RECETA') {
            $q->where(function ($w) {
                $w->where(function ($x) {
                    $x->where('tiene_receta', false)->orWhereNull('tiene_receta');
                })->whereNull('url_pdf_receta');
            });
        } // TODAS => sin filtro extra

        // Rango de fechas (preferimos fecha_atencion; si no, created_at)
        if ($from) {
            $q->where(function ($w) use ($from) {
                $w->whereDate('fecha_atencion', '>=', $from)
                    ->orWhere(function ($x) use ($from) {
                        $x->whereNull('fecha_atencion')->whereDate('created_at', '>=', $from);
                    });
            });
        }
        if ($to) {
            $q->where(function ($w) use ($to) {
                $w->whereDate('fecha_atencion', '<=', $to)
                    ->orWhere(function ($x) use ($to) {
                        $x->whereNull('fecha_atencion')->whereDate('created_at', '<=', $to);
                    });
            });
        }

        // Especialidad (opcional, acepta MED_INT)
        if ($especialidad) {
            $espInfo = $mapEsp($especialidad);
            $q->where(function ($qq) use ($espInfo) {
                $qq->whereRaw('UPPER(especialidad) = ?', [$espInfo['code']])
                    ->orWhere('especialidad', 'LIKE', '%' . $espInfo['label'] . '%');
            });
        }

        // Búsqueda textual sencilla
        if ($qText !== '') {
            $like = '%' . str_replace('%', '\\%', $qText) . '%';
            $q->where(function ($w) use ($like) {
                $w->where('examen_nombre', 'LIKE', $like)
                    ->orWhere('detalle_receta', 'LIKE', $like)
                    ->orWhere('medicamentos_activos', 'LIKE', $like);
            });
        }

        $q->orderByDesc('fecha_atencion')->orderByDesc('created_at');

        $rows = $q->get();

        // -----------------------------
        // 4) Header de paciente (panel-header)
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
        // 5) Agrupar por especialidad -> $grupos (para la vista)
        //    La vista usará un PDF estático: public/recetas/receta-prueba.pdf
        //    (no es necesario pasar 'pdf' por ítem ahora)
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

            $titulo = $r->receta_titulo
                ?? $r->examen_nombre
                ?? ('Receta ' . ($r->examen_codigo ?: '—'));

            $grupos[$code]['items']->push([
                'id'           => $r->id,
                'titulo'       => $titulo,
                'fecha'        => optional($r->fecha_atencion)->format('Y-m-d H:i') ?: optional($r->created_at)->format('Y-m-d H:i'),
                'codigo'       => $r->examen_codigo ?: ($r->receta_codigo ?? '—'),
                'estado'       => ($r->tiene_receta || $r->url_pdf_receta) ? 'DISPONIBLE' : 'PENDIENTE',
                'resumen'      => $r->detalle_receta ?: ($r->impresion_diagnostica ?? '—'),
                'lugar'        => $r->lugar_cita ?: ($r->lugar_emision ?? '—'),
                'profesional'  => $r->id_profesional ?: ($r->profesional_emisor ?? '—'),
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
        // 6) KPIs (sobre los mismos rows)
        // -----------------------------
        $kpis = [
            'total'   => $rows->count(),
            'con_pdf' => $rows->filter(fn($r) => !empty($r->url_pdf_receta) || !empty($r->tiene_receta))->count(),
            'sin_pdf' => $rows->filter(fn($r) => empty($r->url_pdf_receta) && empty($r->tiene_receta))->count(),
        ];

        // -----------------------------
        // 7) Meta para la vista (selects/echo filtros)
        // -----------------------------
        $filtros = [
            'estado'       => $estado,
            'from'         => $from,
            'to'           => $to,
            'q'            => $qText,
            'especialidad' => $especialidad,
        ];

        $estadosDisponibles = ['CON_RECETA', 'SIN_RECETA', 'TODAS'];
        $especialidades     = $rows->pluck('especialidad')
            ->filter()
            ->map(fn($e) => $mapEsp($e)['code'])
            ->unique()
            ->values()
            ->all();

        // -----------------------------
        // 8) Retorno a la vista
        // -----------------------------
        return view('portal.recetas.index', [
            'paciente'           => $paciente,
            'grupos'             => $grupos,         // 👈 para filas (4 cards) y modales
            // (compat: si tienes algo que aún use 'recetas', lo dejo vacío)
            'recetas'            => collect(),       // no lo usa la vista nueva
            'kpis'               => $kpis,
            'filtros'            => $filtros,
            'estadosDisponibles' => $estadosDisponibles,
            'especialidades'     => $especialidades,
        ]);
    }
}
