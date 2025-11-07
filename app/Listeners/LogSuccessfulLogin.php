<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        DB::table('login_log')->insert([
            'user_id'       => $user->id,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'session_id'    => session()->getId(),
            'logged_in_at'  => now(),
            'last_seen_at'  => now(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}
