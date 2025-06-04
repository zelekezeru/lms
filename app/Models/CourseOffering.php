<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseOffering extends Model
{
    protected $guarded = [];
    
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public static function lookUpFor($courseId, $sectionId)
    {
        return CourseOffering::where('course_id', $courseId)->where('section_id', $sectionId)->with('section', 'course')->first();
    }
}
