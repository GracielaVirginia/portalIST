<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class LogSuccessfulLogin
{
    /**
     * Maneja el evento de inicio de sesión exitoso.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        DB::table('login_log')->insert([
            'user_id'    => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'logged_in_at' => now(),
        ]);
    }
}
