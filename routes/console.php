<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Carbon;

Schedule::call(function () {
    $lifetime  = (int) Config::get('session.lifetime', 1); // minutos (solo test)
    $threshold = Carbon::now()->subMinutes($lifetime);

    $rows = DB::table('login_log')
        ->whereNull('logged_out_at')
        ->whereNotNull('last_seen_at')
        ->where('last_seen_at', '<', $threshold)
        ->get();

    foreach ($rows as $row) {
        $end = Carbon::parse($row->last_seen_at) ?: Carbon::parse($row->logged_in_at) ?: now();
        $duration = Carbon::parse($row->logged_in_at)->diffInSeconds($end);

        DB::table('login_log')->where('id', $row->id)->update([
            'logged_out_at'    => $end,
            'duration_seconds' => $duration,
            'close_reason'     => 'timeout',
            'updated_at'       => now(),
        ]);
    }
})->everyMinute(); 
