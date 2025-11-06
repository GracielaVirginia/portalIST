<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Profesional;
use App\Models\Horario;            
use App\Models\Bloqueo;
use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class AgendaPacienteController extends Controller
{
    /** Vista principal: combo con profesionales (sin roles) */
    public function index()
    {
        $profesionales = Profesional::with('tipoProfesional')
            ->orderBy('nombres')
            ->get(['id','idsucursal','idempresa','tipo_profesional_id','nombres','apellidos']);

        return view('agenda.index', [
            'profesionales' => $profesionales,
        ]);
    }

    /** API: businessHours + bloqueos + duración por día (por Profesional) */
    public function apiHorarios($id)
    {
        // $id llega desde /agenda/{id}/horarios
        $profesionalId = (int) $id;

        // Trae horarios + bloqueos (si definiste la relación en Horario -> bloqueos)
        $horarios = Horario::with('bloqueos')
            ->where('profesional_id', $profesionalId)
            ->orderBy('dia_semana')
            ->get();

        Log::info('API horarios', [
            'profesional_id' => $profesionalId,
            'horarios'       => $horarios->count(),
        ]);

        // Mapa día texto → número FullCalendar
        $mapDia = [
            'domingo' => 0,
            'lunes'   => 1,
            'martes'  => 2,
            'miércoles'=> 3, 'miercoles'=> 3,
            'jueves'  => 4,
            'viernes' => 5,
            'sábado'  => 6, 'sabado' => 6,
        ];

        $businessHours  = [];
        $duracionPorDia = [];
        $bloqueosBg     = [];

        foreach ($horarios as $h) {
            $dia = strtolower(trim($h->dia_semana));
            if (!array_key_exists($dia, $mapDia)) {
                Log::warning('Día no reconocido en horario', ['dia_semana' => $h->dia_semana, 'row_id' => $h->id]);
                continue; // ignora filas con día inválido
            }

            // tramo laboral del día → se pinta en morado
            $businessHours[] = [
                'daysOfWeek' => [$mapDia[$dia]],
                'startTime'  => $h->hora_inicio, // '08:30'
                'endTime'    => $h->hora_fin,    // '13:00'
            ];

            // duración por día (para slots)
            $duracionPorDia[$dia] = (int)($h->duracion_bloque ?: 30);

            // bloqueos (opcionales)
            foreach ($h->bloqueos ?? [] as $b) {
                $bloqueosBg[] = [
                    'daysOfWeek' => [$mapDia[$dia]],
                    'startTime'  => $b->inicio,
                    'endTime'    => Carbon::createFromFormat('H:i', $b->inicio)->addMinutes($b->duracion)->format('H:i'),
                    'display'    => 'background',
                    'color'      => '#d1d5db',
                    'title'      => 'Descanso',
                    'groupId'    => 'bloqueo',
                ];
            }
        }

        // fondos grises fuera de horario
        $fuera = $this->buildFueraHorarioBackgrounds($businessHours);

        Log::info('API horarios payload', [
            'bh'   => count($businessHours),
            'blqs' => count($bloqueosBg),
            'out'  => count($fuera),
        ]);

        return response()->json([
            'businessHours'  => $businessHours,
            'bloqueos'       => $bloqueosBg,
            'fueraHorario'   => $fuera,
            'duracionPorDia' => $duracionPorDia,
        ]);
    }

    /** Helper: construye bloques grises fuera de horario */
    private function buildFueraHorarioBackgrounds(array $businessHours): array
    {
        // determina para cada día su inicio/fin laboral
        $byDay = []; // dayNum => ['start'=>HH:MM, 'end'=>HH:MM]
        foreach ($businessHours as $bh) {
            if (!isset($bh['daysOfWeek'][0])) continue;
            $day = $bh['daysOfWeek'][0];

            // si tuvieras múltiples tramos en el mismo día, podrías consolidarlos
            // aquí nos quedamos con el último (como en tu versión)
            $byDay[$day] = ['start' => $bh['startTime'], 'end' => $bh['endTime']];
        }

        $out = [];
        for ($d=0; $d<7; $d++) {
            if (!isset($byDay[$d])) {
                // día completo fuera de horario
                $out[] = [
                    'daysOfWeek' => [$d],
                    'startTime'  => '00:00',
                    'endTime'    => '24:00',
                    'display'    => 'background',
                    'color'      => '#f3f4f6',
                    'groupId'    => 'fuera-horario',
                ];
            } else {
                $s = $byDay[$d]['start']; $e = $byDay[$d]['end'];
                if ($s !== '00:00') {
                    $out[] = [
                        'daysOfWeek'=>[$d], 'startTime'=>'00:00', 'endTime'=>$s,
                        'display'=>'background','color'=>'#f3f4f6','groupId'=>'fuera-horario'
                    ];
                }
                if ($e !== '24:00') {
                    $out[] = [
                        'daysOfWeek'=>[$d], 'startTime'=>$e, 'endTime'=>'24:00',
                        'display'=>'background','color'=>'#f3f4f6','groupId'=>'fuera-horario'
                    ];
                }
            }
        }
        return $out;
    }

    /** API: eventos (citas) ya tomadas por profesional */
    public function apiEventos($id, Request $request)
    {
        $start = $request->query('start'); // ISO que envía FullCalendar
        $end   = $request->query('end');

        $q = Cita::where('profesional_id', (int)$id);
        if ($start) $q->where('fecha', '>=', substr($start,0,10));
        if ($end)   $q->where('fecha', '<=', substr($end,0,10));

        $evs = $q->get()->map(function (Cita $c) {
            $bg = ['pendiente'=>'#fee2e2','confirmada'=>'#fef3c7','atendida'=>'#dcfce7','cancelada'=>'#e5e7eb'][$c->estado] ?? '#e5e7eb';
            $tx = ['pendiente'=>'#b91c1c','confirmada'=>'#b45309','atendida'=>'#15803d','cancelada'=>'#111827'][$c->estado] ?? '#111827';

            return [
                'id'    => $c->id,
                'title' => 'Cita',
                'start' => $c->fecha.'T'.$c->hora_inicio.':00',
                'end'   => $c->fecha.'T'.$c->hora_fin.':00',
                'backgroundColor' => $bg,
                'borderColor'     => $bg,
                'textColor'       => $tx,
                'extendedProps'   => [
                    'estado'        => $c->estado,
                    'tipo_atencion' => $c->tipo_atencion,
                    'motivo'        => $c->motivo,
                ],
            ];
        });

        return response()->json($evs);
    }

    /** Verifica disponibilidad para creación/movimiento */
    public function verificarDisponibilidad(Request $r)
    {
        $r->validate([
            'profesional_id' => 'required|exists:profesionales,id',
            'fecha'          => 'required|date',
            'hora_inicio'    => 'required|date_format:H:i',
            'hora_fin'       => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $ok = $this->disponible(
            (int)$r->profesional_id,
            $r->fecha,
            $r->hora_inicio,
            $r->hora_fin,
            $r->id ?? null
        );

        return response()->json(['disponible' => $ok]);
    }

public function store(Request $r)
{
    $r->validate([
        'profesional_id' => 'required|exists:profesionales,id',
        'fecha'          => 'required|date',
        'hora_inicio'    => 'required|date_format:H:i',
        'hora_fin'       => 'required|date_format:H:i|after:hora_inicio',
        'tipo_atencion'  => 'required|in:presencial,remota',
        'motivo'         => 'nullable|string|max:2000',
    ]);

    // ✅ Trae idsucursal (y idempresa si quieres) del profesional elegido
    $profesional = Profesional::select('idsucursal','idempresa')
        ->findOrFail((int)$r->profesional_id);

    $ok = $this->disponible(
        (int)$r->profesional_id,
        $r->fecha,
        $r->hora_inicio,
        $r->hora_fin
    );

    if (!$ok) {
        return back()
            ->with('error', 'El horario ya no está disponible o cae en descanso.')
            ->withInput();
    }

    Cita::create([
        // puedes priorizar idempresa del profesional si quieres
        'idempresa'      => auth()->user()->idempresa ?? ($profesional->idempresa ?? 1),
        // ✅ aquí va la sucursal del profesional seleccionado
        'idsucursal'     => $profesional->idsucursal,
        'profesional_id' => (int)$r->profesional_id,
        'paciente_id'    => auth()->id(),
        'fecha'          => $r->fecha,
        'hora_inicio'    => $r->hora_inicio,
        'hora_fin'       => $r->hora_fin,
        'motivo'         => $r->motivo,
        'lugar_cita'   => auth()->user()->lugar_cita,
        'tipo_atencion'  => $r->tipo_atencion,
        'estado'         => 'reservada',
    ]);

    return redirect()->route('agenda.index')->with('success','Cita creada');
}


    /** Bloquear un slot libre en una fecha puntual */
    public function bloquearSlot($id, Request $r)
    {
        $r->validate([
            'fecha'    => 'required|date',
            'inicio'   => 'required|date_format:H:i',
            'duracion' => 'required|integer|min:1|max:480',
            'motivo'   => 'nullable|string|max:120',
        ]);

        $diaStr = mb_strtolower(Carbon::parse($r->fecha)->locale('es')->isoFormat('dddd'));

        // Buscar el horario del profesional para ese día
        $horario = Horario::where('profesional_id', (int)$id)
                    ->whereRaw('LOWER(dia_semana)=?', [$diaStr])->first();

        if (!$horario) {
            return response()->json(['success'=>false,'message'=>'El profesional no atiende ese día'], 422);
        }

        $fin = Carbon::createFromFormat('H:i',$r->inicio)->addMinutes($r->duracion)->format('H:i');

        if (!$this->dentroHorario($r->inicio, $fin, $horario->hora_inicio, $horario->hora_fin)) {
            return response()->json(['success'=>false,'message'=>'Está fuera del horario laboral'], 422);
        }

        // No debe chocar con citas
        $disponible = $this->disponible((int)$id, $r->fecha, $r->inicio, $fin);
        if (!$disponible) {
            return response()->json(['success'=>false,'message'=>'Ese tramo está ocupado'], 422);
        }

        Bloqueo::create([
            'idhorario_profesional' => $horario->id,
            'inicio'                => $r->inicio,
            'duracion'              => $r->duracion,
            'fecha'                 => $r->fecha,
            'motivo'                => $r->motivo,
        ]);

        return response()->json(['success'=>true]);
    }

    /** ===== Helpers ===== */

    private function disponible(int $profesionalId, string $fecha, string $hi, string $hf, ?int $excluirId=null): bool
    {
        // 1) Debe caer dentro del horario laboral del día
        $diaStr = mb_strtolower(Carbon::parse($fecha)->locale('es')->isoFormat('dddd'));

        $horario = Horario::where('profesional_id', $profesionalId)
            ->whereRaw('LOWER(dia_semana)=?', [$diaStr])
            ->first();

        if (!$horario) return false;

        if (!$this->dentroHorario($hi, $hf, $horario->hora_inicio, $horario->hora_fin)) {
            return false;
        }

        // 2) No pisa bloqueos (periódicos o puntuales)
        $bloqueos = Bloqueo::where('profesional_id', $horario->id)
            ->where(function($q) use($fecha){
                $q->whereNull('fecha')->orWhere('fecha', $fecha);
            })->get();

        foreach ($bloqueos as $b) {
            $bIni = $b->inicio;
            $bFin = Carbon::createFromFormat('H:i',$b->inicio)->addMinutes($b->duracion)->format('H:i');
            if ($this->overlaps($hi,$hf,$bIni,$bFin)) return false;
        }

        // 3) No pisa otras citas
        $q = Cita::where('profesional_id',$profesionalId)->whereDate('fecha',$fecha);
        if ($excluirId) $q->where('id','!=',$excluirId);

        foreach ($q->get() as $c) {
            if ($this->overlaps($hi,$hf,$c->hora_inicio,$c->hora_fin)) return false;
        }

        return true;
    }

    private function overlaps(string $aStart, string $aEnd, string $bStart, string $bEnd): bool
    {
        // [aStart, aEnd) vs [bStart, bEnd)
        return !($aEnd <= $bStart || $aStart >= $bEnd);
    }

    private function dentroHorario(string $ini, string $fin, string $hStart, string $hEnd): bool
    {
        return ($ini >= $hStart) && ($fin <= $hEnd);
    }

/**
 * API: eventos visibles según perfil del viewer.
 * GET /agenda/{id}/eventos-visibles?start=...&end=...
 */
public function apiEventosVisibles($id, \Illuminate\Http\Request $request)
{
    $profesionalId = (int) $id;

    // Log para confirmar hit
    Log::info('API eventos-visibles HIT', [
        'profesional_id' => $profesionalId,
        'start' => $request->query('start'),
        'end'   => $request->query('end'),
    ]);

    // ==== ¿Viewer es admin? (tabla admin_usuarios) ====
    $viewer  = $request->user();
    $isAdmin = false;

    try {
        if (Schema::hasTable('admin_usuarios') && $viewer) {
            // Preferir user_id si existe; sino probar email
            if (Schema::hasColumn('admin_usuarios', 'user_id')) {
                $isAdmin = DB::table('admin_usuarios')->where('user_id', $viewer->id)->exists();
            } elseif (Schema::hasColumn('admin_usuarios', 'email') && !empty($viewer->email)) {
                $isAdmin = DB::table('admin_usuarios')->where('email', $viewer->email)->exists();
            }
        }
    } catch (\Throwable $e) {
        Log::warning('admin_usuarios check failed', ['err' => $e->getMessage()]);
        $isAdmin = false;
    }

    // ==== Citas del profesional en el rango que pide FullCalendar ====
    $start = $request->query('start'); // ISO
    $end   = $request->query('end');

    $q = Cita::with(['paciente'])
        ->where('profesional_id', $profesionalId);

    if ($start) $q->where('fecha', '>=', substr($start, 0, 10));
    if ($end)   $q->where('fecha', '<=', substr($end,   0, 10));

    $citas = $q->orderBy('fecha')->orderBy('hora_inicio')->get();

    // Colores por estado
    $bgByEstado = [
        'reservada'  => '#fee2e2', // rosa
        'confirmada' => '#fef3c7', // ámbar
        'cancelada'  => '#e5e7eb', // gris
        'atendida'   => '#dcfce7', // verde
    ];
    $txByEstado = [
        'reservada'  => '#b91c1c',
        'confirmada' => '#b45309',
        'cancelada'  => '#111827',
        'atendida'   => '#15803d',
    ];

    $events = $citas->map(function (Cita $c) use ($isAdmin, $bgByEstado, $txByEstado) {
        $estado = $c->estado ?: 'reservada';
        $bg = $bgByEstado[$estado] ?? '#e5e7eb';
        $tx = $txByEstado[$estado] ?? '#111827';

        if ($isAdmin) {
            $pac   = $c->paciente;
            $nombre = trim(($pac->name ?? '') !== '' ? $pac->name : trim(($pac->nombres ?? '').' '.($pac->apellidos ?? '')));
            $rut    = $pac->rut ?? $pac->documento ?? null; // ajusta a tu columna real
            $title  = $rut ? "{$nombre} — {$rut}" : ($nombre ?: 'Paciente');
        } else {
            $title = 'Ocupado';
        }

        // Si tus columnas ya tienen segundos, no añadas ":00"
        $startStr = preg_match('/:\d{2}:\d{2}$/', $c->hora_inicio) ? $c->hora_inicio : ($c->hora_inicio.':00');
        $endStr   = preg_match('/:\d{2}:\d{2}$/', $c->hora_fin)    ? $c->hora_fin    : ($c->hora_fin.':00');

        return [
            'id'    => $c->id,
            'title' => $title,
            'start' => $c->fecha.'T'.$startStr,
            'end'   => $c->fecha.'T'.$endStr,
            'backgroundColor' => $bg,
            'borderColor'     => $bg,
            'textColor'       => $tx,
            'extendedProps'   => [
                'estado'        => $estado,
                'tipo_atencion' => $c->tipo_atencion,
                'visible'       => $isAdmin ? 'admin' : 'paciente',
                'paciente_id'   => $c->paciente_id,
            ],
        ];
    });

    Log::info('API eventos-visibles OUT', [
        'profesional_id' => $profesionalId,
        'count'          => $events->count(),
        'is_admin'       => $isAdmin,
    ]);

    return response()->json($events);
}

}
