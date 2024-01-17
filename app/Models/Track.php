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

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(Distribution::class);
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'track_platform');
    }

    public function authors()
    {
        return $this->hasMany(Author::class);
    }
    public function contributors()
    {
        return $this->hasMany(Contributor::class);
    }
    public function featurings()
    {
        return $this->hasMany(Featuring::class);
    }
    public function composers()
    {
        return $this->hasMany(Composer::class);
    }

    public function producers()
    {
        return $this->hasMany(Producer::class);
    }

    public function musicStores()
    {
        return $this->belongsToMany(MusicStore::class, 'track_music_store')->withPivot('url')->withTimestamps();
    }
}
