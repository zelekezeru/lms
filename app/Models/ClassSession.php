<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date_time' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }
}
