<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicStore extends Model
{
    use HasFactory;

    protected $table = 'music_stores';

    protected $fillable = ['name', 'photo'];
}
