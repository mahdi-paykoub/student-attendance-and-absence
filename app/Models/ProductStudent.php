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
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function checks()
    {
        return $this->hasMany(Check::class);
    }



    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
