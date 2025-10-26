<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'rut',
        'name',
        'email',
        'password',
        'lugar_cita',
        'password_needs_change',
        'is_blocked',
        'failed_login_attempts',
        'blocked_at',
        'failed_validated_attempts',
        'is_validated',
        'theme',
        'added',
        'email_verified_at',
    ];

    /**
     * Los atributos que deben ocultarse en arrays o JSON.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at'      => 'datetime',
        'force_password_change'  => 'boolean',
        'is_blocked'             => 'boolean',
        'is_validated'           => 'boolean',
        'failed_login_attempts'  => 'integer',
    ];

    /**
     * Relación: logs de inicio de sesión del usuario.
     */
    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class);
    }

    /**
     * Relación: último inicio de sesión del usuario.
     */
    public function lastLogin()
    {
        return $this->hasOne(LoginLog::class)->latestOfMany('logged_in_at');
    }

    /**
     * Atributo virtual: determina si el usuario está bloqueado.
     */
    public function getIsLockedAttribute()
    {
        return $this->is_blocked || $this->failed_login_attempts >= 3;
    }
}
