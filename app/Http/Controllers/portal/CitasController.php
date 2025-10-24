<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GestionSaludCompleta;
use Carbon\Carbon;

class CitasController extends Controller
{
    /**
     * Vista de citas del paciente.
     *
     * RUTA: GET /portal/citas  (nombre: portal.citas.index)
     * VISTA sugerida: resources/views/portal/citas/index.blade.php
     *
     * OBJETIVO:
     *  - Listar citas próximas y pasadas del paciente autenticado.
     *  - Exponer filtros básicos (estado, rango de fechas, especialidad).
     *  - Entregar KPIs rápidos (hoy, próximas, pasadas, no realizadas).
     *
     * NOTAS:
     *  - En esta primera versión devolvemos placeholders para que la vista monte.
     *  - Luego reemplazaremos por consultas reales a gestiones_salud_completa.
     */
    public function index(Request $request)
    {
        // -----------------------------
        // 1) Filtros (query params)
        // -----------------------------
        // estado: PROXIMAS | HOY | PASADAS | TODAS
        $estado       = strtoupper($request->query('estado', 'PROXIMAS'));
        $from         = $request->query('from'); // YYYY-MM-DD
        $to           = $request->query('to');   // YYYY-MM-DD
        $especialidad = $request->query('esp');  // ej. RX, LAB, ECO...
        $modalidad    = $request->query('mod');  // PRESENCIAL | REMOTO
        $tipoAtencion = $request->query('tipo'); // CONTROL | PRIMERA | URGENCIA, etc.

        // -----------------------------
        // 2) Base del query (TODO real)
        // -----------------------------
        // TODO: si usas auth, toma el identificador del paciente (RUT/email/ID)
        // $rut = strtoupper($request->user()->rut ?? '');
        // $tipoDoc = 'RUT';
        //
        // $q = GestionSaludCompleta::query()
        //     ->when($rut, fn($qq) => $qq->where('tipo_documento', $tipoDoc)->where('numero_documento', $rut))
        //     ->whereNotNull('fecha_cita_programada'); // sólo registros con cita

        // (Filtros por estado de calendario)
        // $hoy = Carbon::today();
        // if ($estado === 'HOY') {
        //     $q->whereDate('fecha_cita_programada', $hoy);
        // } elseif ($estado === 'PROXIMAS') {
        //     $q->where('fecha_cita_programada', '>=', $hoy->startOfDay());
        // } elseif ($estado === 'PASADAS') {
        //     $q->where('fecha_cita_programada', '<', $hoy->startOfDay());
        // }

        // (Rango de fechas)
        // if ($from) { $q->whereDate('fecha_cita_programada', '>=', $from); }
        // if ($to)   { $q->whereDate('fecha_cita_programada', '<=', $to); }

        // (Filtros adicionales)
        // if ($especialidad) { $q->whereRaw('UPPER(especialidad) = ?', [strtoupper($especialidad)]); }
        // if ($modalidad)    { $q->whereRaw('UPPER(modalidad_atencion) = ?', [strtoupper($modalidad)]); }
        // if ($tipoAtencion) { $q->whereRaw('UPPER(tipo_atencion) = ?', [strtoupper($tipoAtencion)]); }

        // $citas = $q->orderBy('fecha_cita_programada')->get();

        // -----------------------------
        // 3) Placeholders (MVP)
        // -----------------------------
        $citas = collect([
            // Ejemplo de estructura que la vista puede esperar
            // [
            //     'id'                   => 1001,
            //     'fecha_cita_programada'=> '2025-11-03 09:30:00',
            //     'lugar_cita'           => 'Centro Médico Central',
            //     'especialidad'         => 'RX',
            //     'tipo_atencion'        => 'CONTROL',
            //     'modalidad_atencion'   => 'PRESENCIAL',
            //     'estado_solicitud'     => 'CONFIRMADA',
            //     'estado_asistencia'    => 'PENDIENTE', // NO_REALIZADA | REALIZADA
            //     'id_profesional'       => 55,          // (si luego resuelves nombre por join)
            // ],
        ]);

        // -----------------------------
        // 4) KPIs de citas (TODO real)
        // -----------------------------
        // $kpis = [
        //     'hoy'       => (clone $q)->whereDate('fecha_cita_programada', $hoy)->count(),
        //     'proximas'  => (clone $q)->where('fecha_cita_programada', '>=', $hoy->startOfDay())->count(),
        //     'pasadas'   => (clone $q)->where('fecha_cita_programada', '<',  $hoy->startOfDay())->count(),
        //     'no_realizadas' => (clone $q)->where('estado_asistencia', 'NO_REALIZADA')->count(),
        // ];

        $kpis = [
            'hoy'            => 0,
            'proximas'       => 0,
            'pasadas'        => 0,
            'no_realizadas'  => 0,
        ];

        // -----------------------------
        // 5) Meta para la vista
        // -----------------------------
        $filtros = [
            'estado'       => $estado,
            'from'         => $from,
            'to'           => $to,
            'especialidad' => $especialidad,
            'modalidad'    => $modalidad,
            'tipo'         => $tipoAtencion,
        ];

        // -----------------------------
        // 6) Retorno
        // -----------------------------
        return view('portal.citas.index', [
            'citas'   => $citas,
            'kpis'    => $kpis,
            'filtros' => $filtros,

            // (Opcional) valores para selects; puedes poblarlos desde la BD:
            'especialidades' => [], // p.ej. ['RX','LAB','ECO','MED_INT']
            'modalidades'    => ['PRESENCIAL', 'REMOTO'],
            'tipos'          => ['CONTROL', 'PRIMERA', 'URGENCIA'],
        ]);
    }
}
