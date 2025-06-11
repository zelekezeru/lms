<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    protected $guarded = [];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentCategories()
    {
        return $this->belongsTo(PaymentCategory::class);
    }
    public function studyMode()
    {
        return $this->belongsTo(StudyMode::class);
    }
}
