<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAccessAttempt extends Model
{
    protected $fillable = [
        'guard','user_id','username','role','authorized','reason',
        'ip','user_agent','method','path','referer'
    ];
}
