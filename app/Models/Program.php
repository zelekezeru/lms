<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'name',
        'language',
        'code',
        'user_id',
        'duration',
        'description',
    ];

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function director()
    {
        return $this->belongsTo(User::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }

    public function years()
    {
        return $this->hasMany(Year::class);
    }

    public function studyModes()
    {
        return $this->belongsToMany(StudyMode::class)->withPivot('duration');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class)->withPivot('is_common');
    }
}
