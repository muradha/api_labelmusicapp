<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Track extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function distribution(): BelongsTo {
        return $this->belongsTo(Distribution::class);
    }

    public function platforms(): BelongsToMany{
        return $this->belongsToMany(Platform::class, 'track_platform');
    }
}
