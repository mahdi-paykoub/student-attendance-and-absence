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
        return $this->belongsToMany(Student::class)
            ->withPivot([
                'payment_type',
                'pos_type',
                'card_type',
                'check_owner',
                'check_image',
                'check_phone'
            ])
            ->withTimestamps();
    }
}
