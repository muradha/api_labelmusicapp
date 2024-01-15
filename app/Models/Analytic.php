<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function stores()
    {
        return $this->belongsToMany(MusicStore::class, 'analytic_store', 'analytic_id', 'music_store_id')
            ->withPivot(['revenue', 'streaming', 'download'])->withTimestamps();
    }
}
