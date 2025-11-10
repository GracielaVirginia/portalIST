<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\LoginAttempt;

class AuthAttemptsReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input('from') ?: now()->toDateString();
        $dateTo   = $request->input('to')   ?: now()->toDateString();

        $from = \Carbon\Carbon::parse($dateFrom)->startOfDay();
        $to   = \Carbon\Carbon::parse($dateTo)->endOfDay();

        // 1) Resumen por login_input
        $summary = \DB::table('login_attempts as la')
            ->selectRaw('
                TRIM(UPPER(la.login_input)) as login_input,

                -- Login (antes de validar)
                SUM(CASE WHEN la.outcome = "invalid_password" THEN 1 ELSE 0 END)  as invalid_passwords,
                SUM(CASE WHEN la.outcome = "blocked"         THEN 1 ELSE 0 END)  as login_blocked,
                SUM(CASE WHEN la.outcome in ("success","login_success") THEN 1 ELSE 0 END)  as success_login,

                -- Validación (post-login)
                SUM(CASE WHEN la.outcome = "validation_failed"   THEN 1 ELSE 0 END) as validation_failed,
                SUM(CASE WHEN la.outcome = "validation_blocked" THEN 1 ELSE 0 END) as validation_blocked,
                SUM(CASE WHEN la.outcome = "portal_access"      THEN 1 ELSE 0 END) as portal_access,

                -- Técnica
                SUBSTRING_INDEX(GROUP_CONCAT(la.ip_address ORDER BY la.created_at DESC), ",", 1) as last_ip,
                MAX(la.created_at) as last_at
            ')
            ->whereBetween('la.created_at', [$from, $to])
            ->whereNotNull('la.login_input')
            ->groupBy(\DB::raw('TRIM(UPPER(la.login_input))'))
            ->get();

        // 2) Índices de contacto (users.rut)
        $byNumero = DB::table('users')
            ->selectRaw('TRIM(UPPER(rut)) as k, telefono, email')
            ->whereNotNull('rut')
            ->get()->keyBy('k');

        $byRut = DB::table('users')
            ->selectRaw('TRIM(UPPER(rut)) as k, telefono, email')
            ->whereNotNull('rut')
            ->get()->keyBy('k');

        // 3) Derivadas + estado + contacto (solo si NO llegó al home)
        $rows = $summary->map(function ($r) use ($byNumero, $byRut) {
            $r->login_attempts      = (int) $r->invalid_passwords + (int) $r->login_blocked;
            $r->validation_attempts = (int) $r->validation_failed + (int) $r->validation_blocked + (int) $r->portal_access;

            $r->blocked = ($r->login_blocked > 0 || $r->validation_blocked > 0) ? 'Sí' : 'No';

            if ($r->portal_access > 0) {
                $r->status = 'Éxito'; // 🟩 llegó al home
            } elseif ($r->success_login > 0 && $r->portal_access == 0) {
                $r->status = 'Login sin validar'; // 🟨 logró login pero no validó
            } elseif ($r->success_login == 0 && $r->portal_access == 0 && $r->login_attempts > 0) {
                $r->status = 'No pudo loguearse'; // 🟥 nunca logró login
            } elseif ($r->validation_failed > 0 || $r->validation_blocked > 0) {
                $r->status = 'Login sin validar';
            } else {
                $r->status = '—';
            }

            // Contacto: solo para estados no exitosos
            $r->telefono = null;
            $r->email = null;
            if (in_array($r->status, ['No pudo loguearse', 'Login sin validar'])) {
                $key = trim(strtoupper($r->login_input));
                $hit = $byNumero[$key] ?? $byRut[$key] ?? null;
                if ($hit) {
                    $r->telefono = $hit->telefono ?? null;
                    $r->email = $hit->email ?? null;
                }
            }

            return $r;
        })
        ->filter(fn ($r) => in_array($r->status, ['No pudo loguearse', 'Login sin validar', 'Éxito']))
        ->values();

        // 4) Totales para KPIs y gráfica
        $cantNoLogin   = $rows->where('status', 'No pudo loguearse')->count(); // rojo
        $cantNoValida  = $rows->where('status', 'Login sin validar')->count(); // amarillo
        $cantExito     = $rows->where('status', 'Éxito')->count();             // verde

        return view('admin.auth_attempts.index', [
            'dateFrom'     => $dateFrom,
            'dateTo'       => $dateTo,
            'rows'         => $rows,
            'chartLabels'  => ['No pudo loguearse', 'No pudo validarse', 'Llegó al home'],
            'chartData'    => [$cantNoLogin, $cantNoValida, $cantExito],
            // KPIs
            'kpiNoLogin'   => $cantNoLogin,
            'kpiNoValida'  => $cantNoValida,
            'kpiExito'     => $cantExito,
        ]);
    }
}
