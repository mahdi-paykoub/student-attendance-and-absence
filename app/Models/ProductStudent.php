<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStudent extends Model
{
    use HasFactory;
    protected $table = 'product_student';
    protected $fillable = [
        'student_id',
        'product_id',
        'payment_type',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function checks()
    {
        return $this->hasMany(Check::class);
    }
}
