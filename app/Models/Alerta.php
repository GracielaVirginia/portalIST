<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $table = 'alertas';

    protected $fillable = [
        'user_id','tipo','intentos','ip','user_agent','documento','extra','ocurrio_en'
    ];

    protected $casts = [
        'extra' => 'array',
        'ocurrio_en' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
