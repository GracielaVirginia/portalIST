<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginLog extends Model
{
    use HasFactory;

    protected $table = 'login_log';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'session_id',
        'logged_in_at',
        'last_seen_at',
        'logged_out_at',
        'duration_seconds',
        'close_reason',
    ];

    protected $casts = [
        'logged_in_at'      => 'datetime',
        'last_seen_at'      => 'datetime',
        'logged_out_at'     => 'datetime',
        'duration_seconds'  => 'integer',
    ];

    // Relación con User
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // Duración en texto humano
    public function getDurationForHumansAttribute()
    {
        $seconds = $this->duration_seconds
            ?? ($this->logged_out_at
                ? $this->logged_in_at->diffInSeconds($this->logged_out_at)
                : $this->logged_in_at->diffInSeconds(now()));

        if ($seconds < 60) {
            return "{$seconds}s";
        } elseif ($seconds < 3600) {
            return floor($seconds / 60) . "m " . ($seconds % 60) . "s";
        } else {
            $h = floor($seconds / 3600);
            $m = floor(($seconds % 3600) / 60);
            return "{$h}h {$m}m";
        }
    }

    // Si la sesión sigue activa
    public function getIsActiveAttribute()
    {
        return !$this->logged_out_at &&
               ($this->last_seen_at ?? $this->logged_in_at)->gt(
                   now()->subMinutes(config('session.lifetime', 120))
               );
    }
}
