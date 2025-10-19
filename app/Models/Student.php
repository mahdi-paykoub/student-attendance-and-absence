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
        'father_name',
        'national_code',
        'mobile_student',
        'grade_id',
        'major_id',
        'school_id',
        'province_id',
        'city_id',
        'consultant_name',
        'referrer_name',
        'address',
        'phone',
        'mobile_father',
        'mobile_mother',
        'notes'
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
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
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
