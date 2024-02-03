<?php

namespace App\Models;

use Cesargb\Database\Support\CascadeDelete;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankWithdraw extends Model
{
    use HasFactory, CascadeDelete, SoftDeletes;

    protected $table = 'bank_withdraw';

    protected $guarded = ['id'];

    protected $cascadeDeleteMorph = ['withdraw'];

    public function withdraw() : MorphOne {
        return $this->morphOne(Withdraw::class, 'withdrawable');
    }
}
