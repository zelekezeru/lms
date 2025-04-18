<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $guarded = [];

    public function result()
    {
        return $this->hasMany(Result::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
    public function year()
    {
        return $this->belongsTo(Year::class);
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
