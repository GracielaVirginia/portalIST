<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sucursal extends Model
{
    protected $table = 'sucursales';

    protected $fillable = [
        'idempresa',
        'nombre','slug','descripcion',
        'direccion','ciudad','region','telefono','email',
        'visible','orden',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'orden'   => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($m) {
            if (blank($m->slug)) {
                $m->slug = Str::slug($m->nombre);
            }
            if (is_null($m->orden)) {
                $max = static::max('orden');
                $m->orden = is_null($max) ? 1 : $max + 1;
            }
            if (is_null($m->idempresa)) {
                $m->idempresa = auth()->user()->idempresa ?? 1;
            }
        });

        static::updating(function ($m) {
            if ($m->isDirty('nombre') && blank($m->slug)) {
                $m->slug = Str::slug($m->nombre);
            }
        });
    }
}
