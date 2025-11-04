<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bloqueo extends Model
{
    protected $table = 'bloqueos';

    protected $fillable = [
        'idempresa',
        'idsucursal',
        'profesional_id',
        'horario_id',
        'fecha',
        'dia_semana',
        'inicio',
        'duracion',
        'motivo',
    ];

    protected $casts = [
        'idempresa'      => 'integer',
        'idsucursal'     => 'integer',
        'profesional_id' => 'integer',
        'horario_id'     => 'integer',
        'fecha'          => 'date',
        'duracion'       => 'integer',
    ];

    public function profesional()
    {
        return $this->belongsTo(Profesional::class, 'profesional_id', 'id');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id', 'id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'idsucursal', 'id');
    }
}
