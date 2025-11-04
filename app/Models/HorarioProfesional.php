<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioProfesional extends Model
{
    protected $table = 'horarios_profesionales';
    protected $fillable = [
        'idempresa','idprofesional','tipo','dia_semana','hora_inicio','hora_fin','duracion_bloque','firma'
    ];
    public function profesional()
    {
        return $this->belongsTo(Profesional::class, 'idprofesional');
    }
    public function bloqueos()
    {
        return $this->hasMany(Bloqueo::class, 'idhorario_profesional');
    }
}
