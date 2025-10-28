<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketSoporte extends Model
{
    protected $table = 'tickets_soporte';
    protected $fillable = ['email','rut','telefono','detalle','archivo','estado'];
}
