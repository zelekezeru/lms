<?php

namespace App\Models;

use App\Models\User;
use App\Models\Program;
use App\Models\Course;
use App\Models\Instructor;  
use App\Models\Semester;
use App\Models\Year;
use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Section extends Model
{
    protected $guarded = [];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class);
    }
    
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    
    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
    
}
