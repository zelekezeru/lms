<?php

namespace App\Http\Services;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
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
            } elseif ($paymentFilterValue === 'is_scholarship') {
                $query->whereHas('status', function ($q) {
                    $q->where('is_scholarship', true);
                });
            }
        }

        if ($request->has('statuses')) {
            $statusesFilterValue = $request->query('statuses');
            $statusesFilterValue = new Collection($statusesFilterValue);
            $statusesFilterValue = $statusesFilterValue->map(fn($status) => 'is_' . $status);

            $query->whereHas('status', function ($q) use ($statusesFilterValue) {
                foreach ($statusesFilterValue as $status) {
                    $q->where($status, true);
                }
            });
        }
    }
}
