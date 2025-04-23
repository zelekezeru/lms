<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $guarded = [];
    
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'curriculum_course')
            ->withPivot(['year', 'semester', 'course_type'])
            ->withTimestamps();
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
}
