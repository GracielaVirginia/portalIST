<?php
// app/Models/Promocion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use HasFactory;

    protected $table = 'promociones';

    protected $fillable = [
        'titulo',
        'subtitulo',
        'contenido_html',
        'imagen_path',
        'cta_texto',
        'cta_url',
        'activo',
        'destacada',
        'orden',
    ];

    protected $casts = [
        'activo'    => 'boolean',
        'destacada' => 'boolean',
    ];

    // Solo activas y ordenadas
    public function scopeActivas($q)
    {
        return $q->where('activo', true)->orderBy('orden');
    }

    // La destacada activa (para el banner)
    public function scopeDestacada($q)
    {
        return $q->where('activo', true)->where('destacada', true);
    }
}
