<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LugarCita extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'direccion'];

    protected $table = 'lugar_cita';
}
