<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PortalSection extends Model
{
    // Tabla
    protected $table = 'portal_sections';

    // Asignación masiva
    protected $fillable = [
        'page_slug',
        'tipo',
        'titulo',
        'subtitulo',
        'contenido',
        'posicion',
        'visible',
        'publicar_desde',
        'publicar_hasta',
        'updated_by',
    ];

    // Casts
    protected $casts = [
        'contenido'       => 'array',
        'visible'         => 'boolean',
        'posicion'        => 'integer',
        'publicar_desde'  => 'datetime',
        'publicar_hasta'  => 'datetime',
    ];

    // Opcional: tipos conocidos (solo referencia)
    public const TIPO_HERO          = 'hero';
    public const TIPO_BENEFICIOS    = 'beneficios';
    public const TIPO_COMO_FUNCIONA = 'como_funciona';
    public const TIPO_NOVEDADES     = 'novedades';
    public const TIPO_TESTIMONIOS   = 'testimonios';
    public const TIPO_KPIS          = 'kpis';
    public const TIPO_SEGURIDAD     = 'seguridad';

    /* =========================
       Scopes de consulta
       ========================= */

    /**
     * Filtra por página (slug)
     */
    public function scopePagina(Builder $q, string $slug): Builder
    {
        return $q->where('page_slug', $slug);
    }

    /**
     * Solo visibles
     */
    public function scopeVisibles(Builder $q): Builder
    {
        return $q->where('visible', true);
    }

    /**
     * Publicadas dentro de ventana (o sin ventana definida)
     */
    public function scopePublicadas(Builder $q): Builder
    {
        $now = now();
        return $q->where(function ($w) use ($now) {
            $w->whereNull('publicar_desde')->orWhere('publicar_desde', '<=', $now);
        })->where(function ($w) use ($now) {
            $w->whereNull('publicar_hasta')->orWhere('publicar_hasta', '>=', $now);
        });
    }

    /**
     * Orden por posición ascendente
     */
    public function scopeOrdenadas(Builder $q): Builder
    {
        return $q->orderBy('posicion')->orderBy('id');
    }

    /**
     * Conjunto listo para render de una página
     */
    public function scopeParaRender(Builder $q, string $pageSlug): Builder
    {
        return $q->pagina($pageSlug)->visibles()->publicadas()->ordenadas();
    }

    /* =========================
       Helpers
       ========================= */

    /**
     * Devuelve un array indexado por 'tipo' con el primer bloque de cada tipo.
     * Útil si solo renderizas 1 bloque por tipo.
     */
    public static function porTipo(string $pageSlug): array
    {
        return static::paraRender($pageSlug)->get()
            ->groupBy('tipo')
            ->map(fn($group) => $group->first())
            ->toArray();
    }

    /**
     * Devuelve los bloques listos para la vista como colecciones (no arrays),
     * preservando orden y permitiendo múltiples bloques por tipo.
     */
    public static function bloques(string $pageSlug)
    {
        return static::paraRender($pageSlug)->get()->groupBy('tipo');
    }
}
