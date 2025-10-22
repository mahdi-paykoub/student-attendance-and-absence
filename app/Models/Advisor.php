<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advisor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone'];

    // ارتباط با دانش‌آموز
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'advisor_exam');
    }
}
