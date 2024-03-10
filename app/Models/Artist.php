<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artist extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function analytics(): HasMany{
        return $this->hasMany(Analytic::class);
    }

    public function distributions() : BelongsToMany {
        return $this->BelongsToMany(Distribution::class, 'artist_distribution')->withPivot('role');
    }

    public function tracks() : BelongsToMany {
        return $this->BelongsToMany(Track::class, 'artist_track')->withPivot('role');
    }
}
