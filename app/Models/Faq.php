<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faq extends Model
{
    use HasFactory;
protected $table = 'faqs';
    protected $fillable = [
        'question', 'answer', 'category', 'tags', 'is_active', 'sort_order', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
    ];

    /* Scopes */
    public function scopeActive($q)   { return $q->where('is_active', true); }
    public function scopeOrdered($q)  { return $q->orderBy('sort_order')->orderBy('id'); }
    public function scopeSearch($q, ?string $term) {
        if (!$term) return $q;
        return $q->where(function($qq) use ($term) {
            $qq->where('question','like',"%{$term}%")
               ->orWhere('answer','like',"%{$term}%")
               ->orWhere('category','like',"%{$term}%");
        });
    }
}
