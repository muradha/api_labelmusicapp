<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionStore extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribution_id',
        'platforms',
        'territories',
    ];

    protected $casts = [
        'platforms' => 'json',
        'territories' => 'json',
    ];
}
