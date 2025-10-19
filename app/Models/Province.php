<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    // هر استان چند شهر دارد
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    // هر استان چند دانش‌آموز دارد
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
