<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Course;
use Illuminate\Support\Collection;

class DistanceReportExport implements FromCollection, WithHeadings
{
    protected Collection $centers;
    protected Collection $courseCountsByCenter;
    protected Collection $allCourseIds;

    public function __construct(Collection $centers)
    {
        // Eager load necessary relationships for efficiency
        $this->centers = $centers->load('students.grades');
    }

    public function collection(): Collection
    {
        // 1. Get all unique course IDs efficiently
        $this->allCourseIds = $this->centers
            ->flatMap(fn($center) => $center->students)
            ->flatMap(fn($student) => $student->grades)
            ->pluck('course_id')
            ->unique();

        $courses = Course::whereIn('id', $this->allCourseIds)->get();

        $rows = collect();

        // 2. Pre-calculate the student counts for each center and course once
        // This is much more efficient than recalculating in the main loop
        $this->courseCountsByCenter = $this->centers->mapWithKeys(function ($center) {
            $courseCounts = $center->students
                ->flatMap(fn($student) => $student->grades)
                ->pluck('course_id')
                ->countBy();
            return [$center->id => $courseCounts];
        });

        // 3. Build the rows for the report
        foreach ($courses as $course) {
            $row = [
                'No' => $course->id, // Using ID for a stable key is often better
                'Course Name' => $course->name,
                'Total Students' => 0, // Initialize total to zero
            ];

            // Add per-center counts and calculate the total in one pass
            foreach ($this->centers as $center) {
                $count = $this->courseCountsByCenter[$center->id][$course->id] ?? 0;
                $row['Center - ' . $center->id] = $count;
                $row['Total Students'] += $count;
            }

            $rows->push($row);
        }
        return $rows;
    }

    public function headings(): array
    {
        $headings = ['No', 'Course Name', 'Total Students'];
        
        // 4. Ensure headings match the column keys from the collection method
        foreach ($this->centers as $center) {
            $headings[] = 'Center - ' . $center->id;
        }

        return $headings;
    }
}
