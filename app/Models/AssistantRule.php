<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssistantRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','keywords','use_regex','response','match_mode',
        'is_active','sort_order','created_by','updated_by',
    ];

    public function scopeActive($q) {
        return $q->where('is_active', true);
    }

    public function scopeOrdered($q) {
        return $q->orderByRaw('COALESCE(sort_order, 0) ASC')->orderBy('id','ASC');
    }

    /**
     * Devuelve un array de tokens/patrones (separados por coma o salto de línea)
     */
    public function tokens(): array {
        $raw = str_replace(["\r\n","\r"], "\n", $this->keywords ?? '');
        $parts = preg_split('/[,\n]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_map('trim', $parts ?: []));
    }
}
