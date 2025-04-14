<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $guarded = [];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    
    public function sections()
    {
        return $this->hasMany(Section::class);
    }
    
}