<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->hasOne(Status::class);
    }

    public function church()
    {
        return $this->hasOne(Church::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }


    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function paymentSchedules()
    {
        return $this->hasMany(PaymentSchedule::class);
    }
}
