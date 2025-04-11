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
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'user_id');
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
    
}
