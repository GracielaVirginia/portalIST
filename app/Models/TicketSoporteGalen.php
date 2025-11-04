<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketSoporteGalen extends Model
{
    protected $table = 'tickets_soporte_galen'; // <- tabla distinta

    protected $fillable = [
        'email',
        'rut',
        'telefono',
        'detalle',
        'archivo',
        'estado',
    ];
}
