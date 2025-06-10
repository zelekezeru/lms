<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemesterStudent extends Model
{
    protected $table = 'semester_student';

    protected $guarded = [];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function courses()
    {
        return $this->hasMany(CourseStudent::class);
    }

    public function isEligibleForCompletion()
    {
        // Here we should right rules that will check if the student is elligible to complete(set academic_status 'complete') and return true if they are
        return true;
    }
    public function promotionStatus()
    {
        return $this->hasOne(PromotionStatus::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
