<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Section extends Model
{
    protected $guarded = [];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class);
    }
    
    public function semesters()
    {
        return $this->belongsTo(Semester::class);
    }
    
    public function years()
    {
        return $this->belongsTo(Year::class);
    }
    
}
