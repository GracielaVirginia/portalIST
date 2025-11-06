<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginFailure;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LoginAuditController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->query('from');
        $to   = $request->query('to');

        $dateFrom = $from ? Carbon::parse($from)->startOfDay() : Carbon::today();
        $dateTo   = $to   ? Carbon::parse($to)->endOfDay()   : Carbon::today()->endOfDay();

        // KPIs
        $failedTotal = LoginFailure::whereBetween('occurred_at', [$dateFrom,$dateTo])->count();
        $failedUniqueUsers = LoginFailure::whereBetween('occurred_at', [$dateFrom,$dateTo])
            ->distinct()->count('identifier');

        // Agrupación por hora/día para chart (adaptable)
        $groupFormat = $dateFrom->isSameDay($dateTo) ? '%H:00' : '%Y-%m-%d';
        $chartRows = LoginFailure::selectRaw("DATE_FORMAT(occurred_at, '{$groupFormat}') as bucket, COUNT(*) as c")
            ->whereBetween('occurred_at', [$dateFrom,$dateTo])
            ->groupBy('bucket')->orderBy('bucket')->get();

        $chartLabels = $chartRows->pluck('bucket');
        $chartData   = $chartRows->pluck('c');
        $chartTitle  = 'Intentos fallidos en el rango';

        // Tabla paginada
        $logs = LoginFailure::with('user')
            ->whereBetween('occurred_at', [$dateFrom,$dateTo])
            ->orderBy('occurred_at','desc')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.audit.login-intentos', compact(
            'dateFrom','dateTo',
            'failedTotal','failedUniqueUsers',
            'chartLabels','chartData','chartTitle',
            'logs'
        ));
    }
}
