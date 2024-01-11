<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Track extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function distributions(): BelongsToMany {
        return $this->belongsToMany(Distribution::class, 'distribution_tracks');
    }
}
