<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AdminUsuario extends Model
{
    protected $table = 'admin_usuarios';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre_completo',
        'email',
        'rut',
        'user',
        'rol',
        'especialidad',
        'password_hash',
        'activo',
        'ultimo_login',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'activo'       => 'boolean',
        'ultimo_login' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    // Scope para activos
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    // Marcar fecha de último login
    public function marcarUltimoLogin(): void
    {
        $this->ultimo_login = now();
        $this->save(['timestamps' => false]);
    }
}
