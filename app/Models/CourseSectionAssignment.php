<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSectionAssignment extends Model
{
    protected $table = 'course_section';

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
}
