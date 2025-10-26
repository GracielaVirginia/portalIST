<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamenNombre extends Model
{
    use HasFactory;

    protected $fillable = ['codigo', 'nombre', 'tipo', 'especialidad_id'];

    protected $table = 'examen_nombre'; 

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }
}
