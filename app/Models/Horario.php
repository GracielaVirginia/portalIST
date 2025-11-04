<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horarios';

    protected $fillable = [
        'idempresa',
        'idsucursal',
        'profesional_id',
        'tipo',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'duracion_bloque',
    ];

    protected $casts = [
        'idempresa'       => 'integer',
        'idsucursal'      => 'integer',
        'profesional_id'  => 'integer',
        'duracion_bloque' => 'integer',
    ];

    // Relaciones
    public function profesional()
    {
        return $this->belongsTo(Profesional::class, 'profesional_id', 'id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'idsucursal', 'id');
    }

    // Para el próximo paso:
    public function bloqueos()
    {
        return $this->hasMany(Bloqueo::class, 'horario_id', 'id'); // se definirá luego
    }
}
