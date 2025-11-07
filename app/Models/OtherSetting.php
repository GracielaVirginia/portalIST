<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherSetting extends Model
{
    protected $table = 'other_settings';

    protected $fillable = [
        'session_timeout',
        'font_family',
    ];
}
