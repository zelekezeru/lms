<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $guarded = [];

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'user_id');
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public static function getActiveSemester()
    {
        $activeSemester = Semester::where('status', 'active')->first();

        return $activeSemester;
    }

    public function semesterStudent()
    {
        return $this->hasMany(SemesterStudent::class);
    }
}
