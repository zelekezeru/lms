<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $guarded = [];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function head()
    {
        return $this->belongsTo(User::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function curricula()
    {
        return $this->hasMany(Curriculum::class);
    }

    public function getActiveCurricula()
    {
        $curricula = Curriculum::where('year_level', $this->yearLevel())->where('semester_level', Semester::getActiveSemester()->level)->with('course')->where('track_id', $this->id)->get();

        return $curricula;
    }
}
