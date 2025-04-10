<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    protected $fillable = [
        'name',
        'status',
        'is_approved',
        'is_completed',
    ];

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
    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }
    public function programs()
    {
        return $this->hasMany(Program::class);
    }
}
