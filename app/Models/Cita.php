<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';

    protected $fillable = [
        'idempresa','idsucursal','profesional_id','paciente_id',
        'fecha','hora_inicio','hora_fin','tipo_atencion','estado','motivo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function profesional()
    {
        return $this->belongsTo(Profesional::class, 'profesional_id');
    }
}
