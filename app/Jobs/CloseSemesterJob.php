<?php

namespace App\Jobs;

use App\Models\StudyMode;
use App\Models\Semester;
use App\Http\Services\AutoEnrollmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CloseSemesterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $studyModeId;
    protected $newSemesterId;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($studyModeId, $newSemesterId, $userId)
    {
        $this->studyModeId = $studyModeId;
        $this->newSemesterId = $newSemesterId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $selectedStudyMode = StudyMode::findOrFail($this->studyModeId);
        $activeSemester = $selectedStudyMode->activeSemester();

        DB::transaction(function () use ($selectedStudyMode, $activeSemester) {
            // Set the active semester of the study mode to closed
            if ($activeSemester) {
                $selectedStudyMode->semesters()->syncWithoutDetaching([
                    $activeSemester->id => ['status' => 'closed']
                ]);
            }

            // Set the selected semester as the new active semester for the study mode
            $selectedStudyMode->semesters()->syncWithoutDetaching([
                $this->newSemesterId => ['status' => 'active']
            ]);
        });

        // Run the auto-enrollment process
        AutoEnrollmentService::autoEnroll($this->studyModeId, $this->userId);
    }
}
