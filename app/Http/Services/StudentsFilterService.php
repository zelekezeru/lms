<?php

namespace App\Http\Services;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentsFilterService
{
    public static function filterStudents(Request $request, $query)
    {
        if ($request->has('payment')) {
            $paymentFilterValue = $request->query('payment');
            if ($paymentFilterValue === 'paid') {
                $query->whereDoesntHave('payments', function ($q) {
                    $q->whereIn('status', ['pending']);
                });
            } elseif ($paymentFilterValue === 'pending') {
                $query->whereHas('payments', function ($q) {
                    $q->where('status', 'pending');
                })
                    ->whereDoesntHave('payments', function ($q) {
                        $q->where('status', '!=', 'pending');
                    });
            }
        }
    }
}
