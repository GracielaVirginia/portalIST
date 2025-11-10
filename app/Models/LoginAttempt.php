<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $table = 'login_attempts';

    protected $fillable = [
        'user_id',
        'login_input',
        'ip_address',
        'user_agent',
        'outcome',
        'attempt_number',
        'is_blocked',
        'blocked_at',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'blocked_at' => 'datetime',
    ];

    /**
     * Relación con el usuario autenticado (si aplica).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accesor rápido: devuelve si este intento fue un acceso exitoso al portal.
     */
    public function getIsPortalAccessAttribute(): bool
    {
        return $this->outcome === 'portal_access';
    }

    /**
     * Accesor rápido: devuelve si este intento fue un bloqueo (en login o validación).
     */
    public function getIsBlockedAttemptAttribute(): bool
    {
        return in_array($this->outcome, ['blocked', 'validation_blocked']);
    }
}
