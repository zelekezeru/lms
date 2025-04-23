<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddDropRequest extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
