<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'balance',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
