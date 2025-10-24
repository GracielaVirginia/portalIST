<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    use HasFactory;

    // Tabla (opcional si sigue la convención 'noticias')
    protected $table = 'noticias';

    // Asignación masiva
    protected $fillable = [
        'titulo',
        'bajada',
        'contenido',
        'imagen',
        'destacada',
    ];

    // Casts
    protected $casts = [
        'destacada' => 'boolean',
    ];

    /* ================= Scopes útiles ================= */

    // Noticias destacadas primero
    public function scopeDestacadas($query)
    {
        return $query->where('destacada', true);
    }

    // Orden por lo más reciente
    public function scopeRecientes($query)
    {
        return $query->orderByDesc('created_at');
    }

    /* ============== Accessors / Helpers ============== */

    // URL pública de la imagen (si guardas rutas relativas en /storage o /public)
    public function getImagenUrlAttribute(): ?string
    {
        if (!$this->imagen) return null;

        // Si ya es URL absoluta
        if (preg_match('/^https?:\/\//i', $this->imagen)) {
            return $this->imagen;
        }

        // Si guardas en public/...
        return asset($this->imagen);
        // O si usas Storage::disk('public'):
        // return \Storage::disk('public')->url($this->imagen);
    }
}
