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

        if ($request->input('year')) {
            $query->where('year_id', $request->input('year'));
        }
        
        if ($request->input('program')) {
            $query->where('program_id', $request->input('program'));
        }

        if ($request->input('track')) {
            $query->where('track_id', $request->input('track'));
        }

        if ($request->input('study_mode')) {
            $query->where('study_mode_id', $request->input('study_mode'));
        }

        if ($request->input('section')) {
            $query->where('section_id', $request->input('section'));
        }
    }
}
