<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'user_profile';

    public function user(): BelongsTo {
        return $this->BelongsTo(User::class);
    }
}
