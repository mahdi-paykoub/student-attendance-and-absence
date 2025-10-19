<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    use HasFactory;

    protected $fillable = ['student_product_id', 'owner', 'phone', 'image'];

    public function studentProduct()
    {
        return $this->belongsTo(StudentProduct::class);
    }
}
