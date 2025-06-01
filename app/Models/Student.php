<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->hasOne(Status::class);
    }

    public function church()
    {
        return $this->hasOne(Church::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function studyMode()
    {
        return $this->belongsTo(StudyMode::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class)->withPivot('status');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function semesterStudent()
    {
        return $this->hasMany(SemesterStudent::class);
    }

    public function semesters()
    {
        return $this->belongsToMany(Semester::class)->withPivot('status');
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
}
