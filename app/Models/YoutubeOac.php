<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class YoutubeOac extends Model
{
    use HasFactory;

    protected $table = 'youtube_official_artist_channel_service';

    protected $guarded = ['id'];

    public function service() : MorphOne {
        return $this->morphOne(Service::class, 'serviceable');
    }
}
