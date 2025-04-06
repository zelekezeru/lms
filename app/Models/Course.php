<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{ 
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

}
