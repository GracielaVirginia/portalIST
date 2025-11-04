<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesional extends Model
{
    protected $table = 'profesionales';

    protected $fillable = [
        'idempresa',
        'idsucursal',
        'tipo_profesional_id',
        'nombres',
        'apellidos',
        'rut',
        'telefono',
        'email',
        'notas',
    ];

    protected $casts = [
        'idempresa'           => 'integer',
        'idsucursal'          => 'integer',
        'tipo_profesional_id' => 'integer',
    ];

    // Relaciones
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'idsucursal', 'id');
    }

    public function tipoProfesional()
    {
        return $this->belongsTo(TipoProfesional::class, 'tipo_profesional_id', 'id');
    }

    // Para cuando creemos horarios/bloqueos
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'profesional_id', 'id'); // lo definiremos en el siguiente paso
    }
}
