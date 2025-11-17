<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supporter extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
    ];


    public function students()
    {
        return $this->belongsToMany(Student::class, 'supporter_student')
            ->withPivot('assigned_by_id', 'previous_supporter_id', 'status')
            ->withTimestamps();
    }
}
