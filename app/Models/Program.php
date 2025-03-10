<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'name',
        'language',
        'department_id',
        'description'
    ];

    public function studyModes()
    {
        return $this->hasMany(StudyMode::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
