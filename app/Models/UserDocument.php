<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class UserDocument extends Model
{
    protected $table = 'user_documents';

    protected $fillable = [
        'user_id',
        'cita_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'hash_md5',
        'category',
        'label',
        'description',
        'meta',
        'is_reviewed',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'size'        => 'integer',
        'is_reviewed' => 'boolean',
        'meta'        => 'array',
        'reviewed_at' => 'datetime',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Cita::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }

    /**
     * Importante: cuando se elimina el registro, borrar el archivo físico.
     * Esto cubrirá tanto el delete directo como el cascade al eliminar el usuario.
     */
    protected static function booted(): void
    {
        static::deleting(function (UserDocument $doc) {
            if ($doc->path && $doc->disk) {
                try {
                    Storage::disk($doc->disk)->delete($doc->path);
                } catch (\Throwable $e) {
                    // Si falla el borrado físico no bloqueamos la eliminación del registro.
                    // Podrías loguearlo si deseas.
                }
            }
        });
    }
}
