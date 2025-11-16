<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
  use HasFactory;
  protected $fillable = ['name', 'type', 'percentage'];

  public function studentPercentages()
  {
    return $this->hasMany(StudentAccountPercentage::class);
  }


  public function wallet()
  {
    return $this->hasOne(Wallet::class, 'account_id');
  }
}
