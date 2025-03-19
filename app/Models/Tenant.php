<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $guarded = [];

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

}
