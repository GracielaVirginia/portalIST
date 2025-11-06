<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginFailure extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','identifier','ip_address','user_agent','reason','occurred_at','notified',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'notified'    => 'boolean',
    ];

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }
}
