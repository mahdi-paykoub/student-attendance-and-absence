<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    use HasFactory;
    protected $fillable = ['student_product_id', 'date', 'amount', 'serial', 'sayad_code', 'owner_name', 'owner_national_code', 'owner_phone', 'check_image'];

    public function productStudent()
    {
        return $this->belongsTo(ProductStudent::class);
    }
}
