<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'province_id'];

    // هر شهر مربوط به یک استان است
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    // هر شهر چند دانش‌آموز دارد
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
