<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        // 'tenant_id', 
        // 'user_id',
        'title', 
        'description', 
        'image'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
