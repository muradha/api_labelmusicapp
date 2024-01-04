<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Distribution extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'explicit_content' => 'boolean',
        'release_date' => 'datetime:Y-m-d',
        'release_date_original' => 'datetime:Y-m-d',
    ];

    public function artist(): BelongsTo{
        return $this->belongsTo(Artist::class);
    }
}
