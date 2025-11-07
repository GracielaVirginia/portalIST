<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateSessionHeartbeat
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            DB::table('login_log')
                ->where('user_id', auth()->id())
                ->where('session_id', session()->getId())
                ->orderByDesc('logged_in_at')
                ->limit(1)
                ->update([
                    'last_seen_at' => now(),
                    'updated_at'   => now(),
                ]);
        }

        return $next($request);
    }
}
