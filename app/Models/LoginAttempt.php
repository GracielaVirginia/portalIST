<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'user_id','login_input','ip_address','user_agent',
        'outcome','attempt_number','is_blocked','blocked_at',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'blocked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
