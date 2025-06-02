<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Center extends Model
{
    protected $guarded = [];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'center_student');
    }

    public function coordinator()
    {
        return $this->hasOne(Coordinator::class);
    }
}
