<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PortalValidacionConfig extends Model
{
    use HasFactory;

    protected $table = 'portal_validacion_config';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'imagen',
        'activo',
    ];
}
