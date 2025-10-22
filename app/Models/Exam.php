<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'domain_manager',
        'exam_datetime',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function supervisors()
    {
        return $this->belongsToMany(Advisor::class, 'advisor_exam');
    }
}
