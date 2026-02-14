<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class StudentPaymentCodesImport implements ToCollection, WithHeadingRow
{
    public int $updatedCount = 0;
    public int $skippedMissing = 0;
    public int $skippedDuplicate = 0;
    public int $notFound = 0;
    public array $conflicts = [];
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        // Iterate each row and attempt to update the matching student's payment code.
        foreach ($collection as $row) {

            $codeValue = trim($row['payment_code'] ?? '');
            // Try to find student by id_no or old_id
            $student = Student::where(['id_no', $row['id_number']])
                ->get();

            dd($student);
            if (!$student) {
                dd('here');
                // Try numeric id lookup
                if (is_numeric($row['id_number'])) {
                    $student = Student::find(intval($row['id_number']));
                }
            }

            if (!$student) {
                // Student not found; skip
                $this->notFound++;
                continue;
            }

            // Ensure payment code is unique (ignore current student)
            $existing = Student::where('payment_code', $codeValue)->where('id', '!=', $student->id)->first();
            if ($existing) {
                // Conflict: another student already has this code. Skip to avoid db error.
                $this->skippedDuplicate++;
                $this->conflicts[] = [
                    'code' => $codeValue,
                    'student_id' => $student->id,
                    'existing_student_id' => $existing->id,
                ];
                Log::warning('Payment code import skipped - duplicate code', ['code' => $codeValue, 'student_id' => $student->id, 'existing_student_id' => $existing->id]);
                continue;
            }

            // Update student
            $student->payment_code = $codeValue;
            $student->save();
            $this->updatedCount++;
        }
    }

    /**
     * Specify the heading row number.
     *
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getSkippedMissing(): int
    {
        return $this->skippedMissing;
    }

    public function getSkippedDuplicate(): int
    {
        return $this->skippedDuplicate;
    }

    public function getNotFound(): int
    {
        return $this->notFound;
    }

    public function getConflicts(): array
    {
        return $this->conflicts;
    }
}
