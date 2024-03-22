<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

use function Illuminate\Events\queueable;

class Track extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::deleted(queueable(function (Track $audioTrack) {
            if(Storage::disk('public')->exists($audioTrack->file)) Storage::disk('public')->delete($audioTrack->file);
        }));
    }

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(Distribution::class);
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'track_platform')->withTimestamps();
    }

    public function authors()
    {
        return $this->hasMany(Author::class);
    }
    public function contributors(): BelongsToMany
    {
        return $this->belongsToMany(Contributor::class, 'contributor_track')->withPivot('role')->withTimestamps();
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'artist_track')->withPivot('role');
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
