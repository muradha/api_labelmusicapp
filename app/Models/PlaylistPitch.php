<?php

namespace App\Models;

use Cesargb\Database\Support\CascadeDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PlaylistPitch extends Model
{
    use HasFactory, CascadeDelete;

    protected $table = 'playlist_pitch_service';

    protected $guarded = ['id'];

    protected $cascadeDeleteMorph = ['service'];

    public function service() : MorphOne {
        return $this->morphOne(Service::class, 'serviceable');
    }
}
