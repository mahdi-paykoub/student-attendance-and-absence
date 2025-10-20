<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'price',
        'tax_percent',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'product_student')
            ->withPivot('payment_type')
            ->withTimestamps();
    }
}
