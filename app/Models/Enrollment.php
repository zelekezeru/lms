<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classSchedules()
    {
        return $this->hasManyThrough(ClassSchedule::class, CourseOffering::class);
    }

    public function classSessions()
    {
        return $this->hasManyThrough(ClassSession::class, CourseOffering::class);
    }

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }
}
