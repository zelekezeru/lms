<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $guarded = [];

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }
}
