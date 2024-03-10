<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Distribution extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'explicit_content' => 'boolean',
        'release_date' => 'datetime:Y-m-d',
        'release_date_original' => 'datetime:Y-m-d',
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function tracks(): HasMany {
        return $this->hasMany(Track::class);
    }

    public function store(): HasOne {
        return $this->hasOne(DistributionStore::class, 'distribution_id');
    }

    public function artists() : BelongsToMany {
        return $this->belongsToMany(Artist::class, 'artist_distribution')->withPivot('role')->withTimestamps();
    }
}
