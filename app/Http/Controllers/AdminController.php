<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends Controller
{

    public function dashboard()
    {
        // Estadísticas para el dashboard - USANDO CAMPOS REALES
        $usersCount = User::count();
        $activeUsersCount = User::where('is_blocked', false)->count(); // is_blocked en lugar de is_active
        
        // Usuarios bloqueados - USANDO CAMPOS REALES
        $blockedUsers = User::where('is_blocked', true)
                           ->orWhere('failed_login_attempts', '>=', 3) // failed_login_attempts en lugar de login_attempts
                           ->get();

        // Registros de login de hoy
        $loginLogs = LoginLog::with('user')
                            ->whereDate('logged_in_at', today())
                            ->orderBy('logged_in_at', 'desc')
                            ->get();

        // Sesiones por hora para el gráfico
        $loginLogsPerHour = $this->getLoginStatsPerHour();

        return view('admin.dashboard', compact(
            'usersCount',
            'activeUsersCount',
            'blockedUsers',
            'loginLogs',
            'loginLogsPerHour'
        ));
    }

    public function records()
    {
        $loginLogs = LoginLog::with('user')
                            ->orderBy('logged_in_at', 'desc')
                            ->paginate(20);

        return view('admin.records', compact('loginLogs'));
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function getLoginStatsPerHour()
    {
        $stats = [];
        
        for ($hour = 0; $hour < 24; $hour++) {
            $startHour = today()->addHours($hour);
            $endHour = today()->addHours($hour + 1);
            
            $count = LoginLog::whereBetween('logged_in_at', [$startHour, $endHour])
                           ->count();
            
            $stats[] = $count;
        }

        return $stats;
    }
}