<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'name',
        'study',
        'language',
        'description'
    ];

}
