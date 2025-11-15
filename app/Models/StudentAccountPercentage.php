<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentAccountPercentage extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'account_id', 'percentage'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
