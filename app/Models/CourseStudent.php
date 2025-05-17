<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseStudent extends Model
{
    public function semesterStudent()
    {
        return $this->belongsTo(SemesterStudent::class);
    }
}
