<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $table = 'login_log';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'logged_in_at',
        'updated_at',
        'created_at',
    ];

    // Asegúrate de que logged_in_at sea un objeto Carbon
    protected $casts = [
        'logged_in_at' => 'datetime',
    ];

    // Relación con User (opcional, pero útil)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}