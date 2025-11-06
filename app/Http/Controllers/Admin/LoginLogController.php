<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LoginLogController extends Controller
{
    public function index(Request $request)
    {
        // Filtros (por defecto: hoy)
        $dateFrom = $request->query('from', Carbon::today()->toDateString());
        $dateTo   = $request->query('to',   Carbon::today()->toDateString());

        $from = Carbon::parse($dateFrom)->startOfDay();
        $to   = Carbon::parse($dateTo)->endOfDay();

        // === Chart: logins por HORA (solo si el rango es un día; si no, agregamos por día)
        $isSingleDay = $from->isSameDay($to);

        if ($isSingleDay) {
            // Preparamos 24 posiciones (00–23)
            $hours   = range(0, 23);
            $baseMap = array_fill_keys($hours, 0);

            $rows = LoginLog::whereBetween('logged_in_at', [$from, $to])
                ->selectRaw('HOUR(logged_in_at) as h, COUNT(*) as c')
                ->groupBy('h')
                ->orderBy('h')
                ->pluck('c', 'h')
                ->toArray();

            $byHour = array_replace($baseMap, $rows);
            $chartLabels = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT).':00', array_keys($byHour));
            $chartData   = array_values($byHour);
            $chartTitle  = 'Logins de hoy por hora';
        } else {
            // Cuando filtras varios días: agregamos por día
            $rows = LoginLog::whereBetween('logged_in_at', [$from, $to])
                ->selectRaw('DATE(logged_in_at) as d, COUNT(*) as c')
                ->groupBy('d')
                ->orderBy('d')
                ->get();

            $chartLabels = $rows->pluck('d')->map(fn($d) => Carbon::parse($d)->format('d-m'))->all();
            $chartData   = $rows->pluck('c')->all();
            $chartTitle  = 'Logins por día';
        }

        // Métricas rápidas
        $totalLogins = LoginLog::whereBetween('logged_in_at', [$from, $to])->count();
        $uniqueUsers = LoginLog::whereBetween('logged_in_at', [$from, $to])->distinct('user_id')->count('user_id');

        // Tabla (JOIN users para mostrar nombre/email)
        $logs = LoginLog::with(['user:id,name,email'])
            ->whereBetween('logged_in_at', [$from, $to])
            ->orderByDesc('logged_in_at')
            ->paginate(50)
            ->withQueryString();

        return view('admin.login_logs.index', [
            'dateFrom'     => $dateFrom,
            'dateTo'       => $dateTo,
            'isSingleDay'  => $isSingleDay,
            'chartLabels'  => $chartLabels,
            'chartData'    => $chartData,
            'chartTitle'   => $chartTitle,
            'totalLogins'  => $totalLogins,
            'uniqueUsers'  => $uniqueUsers,
            'logs'         => $logs,
        ]);
    }
}
