<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerificationRequest extends Model
{
    protected $table = 'email_verification_requests';

    protected $fillable = [
        'email',
        'code',
        'status',
        'attempts',
        'last_error',
        'ip',
        'user_agent',
        'sent_at',
        'verified_at',
    ];

    protected $casts = [
        'sent_at'     => 'datetime',
        'verified_at' => 'datetime',
    ];
}
