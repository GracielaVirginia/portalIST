<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrigenSolicitud extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $table = 'origen_solicitud';
}
