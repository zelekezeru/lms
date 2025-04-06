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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    

    public function students()
    {
        return $this->hasMany(Student::class);
    }


    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
