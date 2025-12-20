<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsReport extends Model
{
    use HasFactory;

    use HasFactory;
    protected $fillable = [
        'student_id',
        'template_id',
        'to',
        'body',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function template()
    {
        return $this->belongsTo(SmsTemplate::class, 'template_id');
    }
}
