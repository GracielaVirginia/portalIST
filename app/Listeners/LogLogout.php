<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogLogout
{
    public function handle(Logout $event): void
    {
        $userId = $event->user->id ?? null;
        $sessionId = session()->getId();

        $row = DB::table('login_log')
            ->where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->orderByDesc('logged_in_at')
            ->first();

        if ($row && !$row->logged_out_at) {
            $end = now();
            $duration = Carbon::parse($row->logged_in_at)->diffInSeconds($end);

            DB::table('login_log')
                ->where('id', $row->id)
                ->update([
                    'logged_out_at'    => $end,
                    'duration_seconds' => $duration,
                    'close_reason'     => 'logout',
                    'updated_at'       => $end,
                ]);
        }
    }
}
