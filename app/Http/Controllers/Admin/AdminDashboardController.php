<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginLog;
use App\Models\Noticia;         
use App\Models\AdminUsuario;    
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;             
use Illuminate\Support\Facades\Log;
use App\Models\GestionSaludCompleta;
use Illuminate\Support\Facades\App;   

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Ajusta si tu app usa otra zona horaria
        $tz      = config('app.timezone', 'America/Santiago');
        $hoy     = Carbon::now($tz)->startOfDay();
        $mañana  = (clone $hoy)->copy()->endOfDay(); // fin del día

        // Sesiones únicas de hoy (sin repetir usuario)
        $sesionesUnicasHoy = LoginLog::query()
            ->whereBetween('logged_in_at', [$hoy, $mañana])
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        // Usuarios registrados hoy
        $registradosHoy = User::query()
            ->whereBetween('created_at', [$hoy, $mañana])
            ->count();

        // Usuarios bloqueados hoy (si usas blocked_at o similar)
        //  - Si quieres "bloqueados vigentes", usa: where('blocked_at', '>', now())
        $bloqueadosHoy = User::query()
            ->whereNotNull('blocked_at')
            ->whereBetween('blocked_at', [$hoy, $mañana])
            ->count();

        // Bloqueados vigentes para la tabla (opcional)
        $blockedUsers = User::query()
            ->whereNotNull('blocked_at')
            ->where('blocked_at', '>', Carbon::now($tz))
            ->get();

        // Login logs por hora (para el gráfico)
        $porHora = LoginLog::query()
            ->selectRaw('HOUR(CONVERT_TZ(logged_in_at, "+00:00", "'.Carbon::now($tz)->format('P').'")) as h, COUNT(*) as c')
            ->whereBetween('logged_in_at', [$hoy, $mañana])
            ->groupBy('h')
            ->pluck('c', 'h')
            ->toArray();

        $loginLogsPerHour = [];
        for ($i = 0; $i < 24; $i++) {
            $loginLogsPerHour[] = (int)($porHora[$i] ?? 0);
        }

        // Totales para el sidebar
        $usuariosTotal        = User::count();
        $noticiasTotal        = Noticia::count();
        $administradoresTotal = AdminUsuario::count();

        // Si tienes un modelo/tabla para validaciones, cámbialo aquí:
        $validacionesTotal    = 0; // \App\Models\Validacion::count();

        // También puedes querer pasar estos a la vista si los usas en tarjetas/resumen:
        $usersCount        = $usuariosTotal;
        $activeUsersCount  = User::whereNull('blocked_at')->count();
        $loginLogs         = LoginLog::query()
            ->whereBetween('logged_in_at', [$hoy, $mañana])
            ->latest('logged_in_at')
            ->get();

        return view('admin.dashboard', [
            // Sidebar
            'sidebarStats'       => [
                'usuarios'        => $usuariosTotal,
                'noticias'        => $noticiasTotal,
                'administradores' => $administradoresTotal,
                'validaciones'    => $validacionesTotal,
            ],
            // Métricas del día (por si las usas en la página)
            'sesionesUnicasHoy'  => $sesionesUnicasHoy,
            'registradosHoy'     => $registradosHoy,
            'bloqueadosHoy'      => $bloqueadosHoy,
            'blockedUsers'       => $blockedUsers,
            'loginLogsPerHour'   => $loginLogsPerHour,
            'loginLogs'          => $loginLogs,
            'usersCount'         => $usersCount,
            'activeUsersCount'   => $activeUsersCount,
        ]);
    }

public function searchUsers(Request $request)
{
    $request->validate([
        'query' => 'required|string|min:2'
    ]);

    $raw     = trim($request->query('query', ''));
    $like    = '%'.str_replace('%', '', $raw).'%';
    $rutNorm = strtoupper(str_replace(['.', ' '], '', $raw));
    $rutLike = '%'.$rutNorm.'%';

    Log::info('[ADMIN][SEARCH] IN', ['raw' => $raw]);

    // 1) Buscar en GESTIONES (LIKE por nombre y rut)
    $rows = DB::table('gestiones_salud_completa')
        ->selectRaw('
            TRIM(nombre_paciente) AS nombre,
            UPPER(REPLACE(REPLACE(numero_documento, ".", ""), " ", "")) AS rut,
            TRIM(email) AS email
        ')
        ->where(function ($q) use ($like, $rutLike) {
            $q->where('nombre_paciente', 'like', $like)
              ->orWhere('numero_documento', 'like', $like)
              ->orWhereRaw('UPPER(REPLACE(REPLACE(numero_documento, ".", ""), " ", "")) LIKE ?', [$rutLike]);
        })
        ->distinct()
        ->orderByRaw('1 asc') // 1 = nombre
        ->limit(10)
        ->get();

    // 2) Normalizar RUTs obtenidos y consultar en USERS si existen
    $result = [];
    foreach ($rows as $r) {
        $rut = $r->rut ?? '';
        $nombre = $r->nombre ?? '';
        $emailGestiones = $r->email ?? '';

        // Busca en users por RUT normalizado
        $user = User::whereRaw('UPPER(REPLACE(REPLACE(rut, ".", ""), " ", "")) = ?', [$rut])->first();

        $portalRegistrado = (bool) $user;
        $emailPortal = $user->email ?? null;

        // Si en gestiones el email viene vacío y en users existe, usamos el de users para mostrar
        $emailMostrar = $emailGestiones ?: ($emailPortal ?: '');

        $result[] = [
            'nombre'             => $nombre,
            'rut'                => $rut,
            'email'              => $emailMostrar,
            'portal_registrado'  => $portalRegistrado,
            'email_portal'       => $emailPortal,
        ];
    }

    Log::info('[ADMIN][SEARCH] OUT', ['count' => count($result)]);

    return response()->json($result);
}

public function stats(Request $request)
{
    $fecha = $request->string('fecha')->toString();   // 'YYYY-MM-DD'
    $sede  = $request->string('sede')->toString() ?: null;

    if (!$fecha) {
        return response()->json(['ok'=>false,'error'=>'Falta parámetro fecha'], 422);
    }

    // Rango exacto del día según timezone de la app
    $ini = Carbon::parse($fecha, config('app.timezone'))->startOfDay();
    $fin = Carbon::parse($fecha, config('app.timezone'))->endOfDay();

    // ---- SEDES: unir sedes detectadas en ambas tablas (y limpiar nulos/dupes)
    $sedesGest = GestionSaludCompleta::query()
        ->whereNotNull('lugar_cita')
        ->distinct()->orderBy('lugar_cita')
        ->pluck('lugar_cita');

    // OJO: en users puede llamarse lugar_cita o lugaR_cita (typo). Tomamos ambas.
    $sedesUsers = \App\Models\User::query()
        ->where(function($q){
            $q->whereNotNull('lugar_cita')
              ->orWhereNotNull('lugar_cita');
        })
        ->selectRaw("COALESCE(lugar_cita, lugar_cita) as sede")
        ->distinct()->orderBy('sede')
        ->pluck('sede');

    $sedes = $sedesGest->merge($sedesUsers)->filter()->unique()->values();

    // ---- QUERIES con filtro de sede y rango de fecha
    // Usuarios registrados (created_at)
    $qUsuarios = \App\Models\User::query()
        ->whereBetween('created_at', [$ini, $fin])
        ->when($sede, function ($q) use ($sede) {
            $q->where(function ($qq) use ($sede) {
                $qq->where('lugar_cita', $sede)
                   ->orWhere('lugar_cita', $sede); // fallback por si el campo tiene el typo
            });
        });

    $usuariosRegistrados = $qUsuarios->count();

    // Exámenes realizados (created_at) en gestiones_salud_completa
    $qExamenes = GestionSaludCompleta::query()
        ->whereBetween('created_at', [$ini, $fin])
        ->when($sede, fn($q) => $q->where('lugar_cita', $sede));

    $examenesRealizados = $qExamenes->count();

    // Usuarios bloqueados (is_blocked=1 y blocked_at en la fecha)
    $qBloqueados = \App\Models\User::query()
        ->where('is_blocked', 1)
        ->whereBetween('blocked_at', [$ini, $fin])
        ->when($sede, function ($q) use ($sede) {
            $q->where(function ($qq) use ($sede) {
                $qq->where('lugar_cita', $sede)
                   ->orWhere('lugar_cita', $sede);
            });
        });

    $usuariosBloqueados = $qBloqueados->count();

    // ---- LOG para inspección rápida en storage/logs/laravel.log
    Log::info('[dashboard.stats]', [
        'fecha' => $fecha,
        'sede'  => $sede,
        'ini'   => $ini->toDateTimeString(),
        'fin'   => $fin->toDateTimeString(),
        'usuariosRegistrados' => $usuariosRegistrados,
        'examenesRealizados'  => $examenesRealizados,
        'usuariosBloqueados'  => $usuariosBloqueados,
    ]);

    // ---- Respuesta
    $resp = [
        'ok' => true,
        'fecha' => $fecha,
        'sede'  => $sede,
        'sedes' => $sedes,
        'usuariosRegistrados' => $usuariosRegistrados,
        'examenesRealizados'  => $examenesRealizados,
        'usuariosBloqueados'  => $usuariosBloqueados,
    ];

    // Info adicional de depuración SOLO en local
    if (App::environment('local')) {
        $resp['debug'] = [
            'range' => [$ini->toIso8601String(), $fin->toIso8601String()],
            'has_sede_filter' => (bool)$sede,
            'samp_users' => \App\Models\User::whereBetween('created_at', [$ini,$fin])->limit(3)->pluck('id'),
            'samp_gest'  => GestionSaludCompleta::whereBetween('created_at', [$ini,$fin])->limit(3)->pluck('id'),
            'samp_block' => \App\Models\User::where('is_blocked',1)->whereBetween('blocked_at', [$ini,$fin])->limit(3)->pluck('id'),
        ];
    }

    return response()->json($resp);
}
public function statsBySede(Request $request)
{
    try {
        $fecha = $request->string('fecha')->toString();
        if (!$fecha) {
            return response()->json(['ok'=>false,'error'=>'Falta parámetro fecha'], 422);
        }

        $ini = Carbon::parse($fecha, config('app.timezone'))->startOfDay();
        $fin = Carbon::parse($fecha, config('app.timezone'))->endOfDay();

        // Sedes (ambas tablas)
        $sedesGest = GestionSaludCompleta::query()
            ->whereNotNull('lugar_cita')
            ->distinct()->orderBy('lugar_cita')
            ->pluck('lugar_cita');

        $sedesUsers = User::query()
            ->where(function($q){
                $q->whereNotNull('lugar_cita')
                  ->orWhereNotNull('lugar_cita');
            })
            ->selectRaw("COALESCE(lugar_cita, lugar_cita) as sede")
            ->distinct()->orderBy('sede')
            ->pluck('sede');

        $sedesAll = $sedesGest->merge($sedesUsers)->filter()->unique()->values();

        // Conteo por sede — Exámenes (gestiones_salud_completa)
        $examenesPorSede = GestionSaludCompleta::query()
            ->whereBetween('created_at', [$ini, $fin])
            ->whereNotNull('lugar_cita')
            ->selectRaw('lugar_cita as sede, COUNT(*) as total')
            ->groupBy('lugar_cita')
            ->pluck('total', 'sede'); // ['SEDE A' => 10, ...]

        // Conteo por sede — Usuarios registrados (users)
        $usuariosPorSede = User::query()
            ->whereBetween('created_at', [$ini, $fin])
            ->where(function($q){
                $q->whereNotNull('lugar_cita')
                  ->orWhereNotNull('lugar_cita');
            })
            ->selectRaw('COALESCE(lugar_cita, lugar_cita) as sede, COUNT(*) as total')
            ->groupBy('sede')
            ->pluck('total', 'sede');

        // Armar series unificadas por sede
        $series = [];
        foreach ($sedesAll as $sede) {
            $series[] = [
                'sede' => $sede,
                'examenes' => (int) ($examenesPorSede[$sede] ?? 0),
                'usuarios' => (int) ($usuariosPorSede[$sede] ?? 0),
            ];
        }

        Log::info('[dashboard.stats.bySede]', [
            'fecha' => $fecha,
            'ini' => $ini->toDateTimeString(),
            'fin' => $fin->toDateTimeString(),
            'sedes' => $sedesAll->count(),
        ]);

        return response()->json([
            'ok' => true,
            'fecha' => $fecha,
            'sedes' => $sedesAll,
            'series' => $series, // [{sede, examenes, usuarios}, ...]
        ]);
    } catch (\Throwable $e) {
        Log::error('[dashboard.stats.bySede][EX]', ['msg'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()]);
        $payload = ['ok'=>false,'error'=>'Error interno'];
        if (app()->environment('local')) {
            $payload['debug'] = ['message'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()];
        }
        return response()->json($payload, 500);
    }
}

}

