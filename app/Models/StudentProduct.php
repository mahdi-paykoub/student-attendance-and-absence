<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProduct extends Model
{
    use HasFactory;

    protected $table = 'student_product';

    protected $fillable = ['student_id', 'product_id', 'payment_type', 'pos_type', 'card_type'];

    public function checks()
    {
        return $this->hasMany(Check::class);
    }
}
