<?php

namespace App\Http\Services;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentsFilterService
{
    public static function filterStudents(Request $request, $query)
    {
        // dd($request->query('payment'));
        if ($request->has('payment')) {
            if ($request->query('payment') == 'paid') {
                $query->whereDoesntHave('payments', function ($q) {
                    $q->where('status', '!=', 'completed');
                });
            }
        }
    }
}
