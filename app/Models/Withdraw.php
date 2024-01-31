<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Withdraw extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function withdrawable(): MorphTo{
        return $this->morphTo();
    }
}
