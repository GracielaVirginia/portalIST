<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class TipoProfesional extends Model
{
    protected $table = 'tipos_profesionales';

    protected $fillable = [
        'idempresa',
        'idsucursal',
        'nombre',
        'descripcion',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($m) {
            if (blank($m->slug)) {
                $m->slug = Str::slug($m->nombre);
            }
            if (is_null($m->idempresa)) {
                $m->idempresa = 1;
            }
        });

        static::updating(function ($m) {
            if ($m->isDirty('nombre') && blank($m->slug)) {
                $m->slug = Str::slug($m->nombre);
            }
        });
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'idsucursal', 'id');
    }
}
