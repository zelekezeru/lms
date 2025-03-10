<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyMode extends Model
{
    protected $guarded =[];

    public function Program()
    {
        return $this->belongsTo(Program::class);
    }
}
