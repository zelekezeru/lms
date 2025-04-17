<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{ 
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class);
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class);
    }

    public function departmentS(): BelongsToMany
    {
        return $this->belongsToMany(Department::class);
    }
    
    public function years()
    {
        return $this->belongsTo(Year::class);
    }

    public function semesters()
    {
        return $this->belongsTo(Semester::class);
    }

    public function programs()
    {
        return $this->belongsToMany(Program::class);
    }

}
