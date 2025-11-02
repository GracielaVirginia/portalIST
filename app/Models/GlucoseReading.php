<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlucoseReading extends Model
{
  protected $fillable = ['user_id','fecha','valor','nota'];
  protected $casts = ['fecha' => 'date'];

  public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
