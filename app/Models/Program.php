<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function user()
    {
        return $this->director();
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
        return $this->hasMany(StudyMode::class);
    }
    
}
