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
        'grade_id',
        'major_id',
        'is_active',
        'is_shared'
    ];



    public function productStudents()
    {
        return $this->hasMany(ProductStudent::class);
    }


    public function students()
    {
        return $this->belongsToMany(Student::class, 'product_student')->withPivot('id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}
