<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;
      protected $fillable = [
        'title',
        'image',
        'amount',
        'account_id',
        'paid_at',
    ];

    protected $dates = ['paid_at'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
