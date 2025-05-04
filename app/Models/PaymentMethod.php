<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $guarded = [];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    public function paymentCategories()
    {
        return $this->hasMany(PaymentCategory::class);
    }
}
