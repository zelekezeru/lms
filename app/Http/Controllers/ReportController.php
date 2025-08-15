<?php

namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\Center;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Inertia\Inertia;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function downloadDistanceReportPDF()
    {
        $centers = Center::with('students.grades')->get();

        // Get all unique course IDs
        $allCourseIds = $centers
            ->flatMap(fn($center) => $center->students)
            ->flatMap(fn($student) => $student->grades)
            ->pluck('course_id')
            ->unique();

        $courses = Course::whereIn('id', $allCourseIds)->get();

        // Pre-calculate counts
        $courseCountsByCenter = $centers->mapWithKeys(function ($center) {
            $courseCounts = ($center->students ?? collect())
                ->flatMap(fn($student) => $student->grades ?? collect())
                ->pluck('course_id')
                ->countBy();
            return [$center->id => $courseCounts];
        });

        // Build rows
        $rows = collect();
        foreach ($courses as $course) {
            $total = 0;
            $row = [
                'no' => $course->id,
                'name' => $course->name,
            ];

            foreach ($centers as $center) {
                $count = $courseCountsByCenter[$center->id][$course->id] ?? 0;
                $total += $count;
                $row['center_'.$center->id] = $count;
            }

            $row['total'] = $total;
            $rows->push($row);
        }

        return Inertia::render('Centers/CenterCoursesReport', [
            'centers' => $centers,
            'rows' => $rows
        ]);
    }
}
