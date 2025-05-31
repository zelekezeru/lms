<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $guarded = [];
    
    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class)->where('semester_id', Semester::getActiveSemester()->id);
    }
}
