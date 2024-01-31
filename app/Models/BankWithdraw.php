<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BankWithdraw extends Model
{
    use HasFactory;

    protected $table = 'bank_withdraw';

    protected $guarded = ['id'];

    public function withdraw() : MorphMany {
        return $this->morphMany(Withdraw::class, 'withdrawable');
    }
}
