<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
