<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'photo',
        'first_name',
        'last_name',
        'gender',
        'father_name',
        'national_code',
        'mobile_student',
        'grade_id',
        'major_id',
        'school_id',
        'province',
        'city',
        'consultant_id',
        'referrer_id',
        'address',
        'phone',
        'mobile_father',
        'mobile_mother',
        'notes',
        'seat_number',
        'custom_date',
        'birthday'
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    public function major()
    {
        return $this->belongsTo(Major::class);
    }
    public function school()
    {
        return $this->belongsTo(School::class);
    }
    public function advisor()
    {
        return $this->belongsTo(Advisor::class);
    }



    public function productStudents()
    {
        return $this->hasMany(ProductStudent::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_student')
            ->withPivot('payment_type')
            ->withTimestamps();
    }
}
