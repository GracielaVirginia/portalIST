<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    // Nombre de la tabla
    protected $table = 'images';

    // Campos asignables
    protected $fillable = ['nombre', 'seleccionada'];
    // Atributos agregados al JSON/array del modelo
    protected $appends = ['url'];

    /**
     * URL pública de la imagen en /public/images
     */
    public function getUrlAttribute(): string
    {
        return asset('images/' . $this->nombre);
    }

    /**
     * Ruta absoluta en el filesystem (útil para validaciones)
     */
    public function getAbsolutePathAttribute(): string
    {
        return public_path('images/' . $this->nombre);
    }

    /**
     * Scope: ordenar por nombre (útil para la galería)
     */
    public function scopeOrdenPorNombre($query)
    {
        return $query->orderBy('nombre');
    }
}
