<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeComplaint;
use App\Models\GradeAuditLog;
use App\Models\Instructor;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use App\Models\Weight;
use App\Models\Year;
use App\Models\Tenant;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class GradeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $instructorUser;
    protected User $studentUser;
    protected Instructor $instructor;
    protected Student $student;
    protected Year $year;
    protected Semester $semester;
    protected Section $section;
    protected Course $course;
    protected CourseOffering $offering;
    protected function setUp(): void
    {
        parent::setUp();

        // Run DatabaseSeeder to seed the entire DB schema
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        // Fetch seeded records
        $this->year = Year::where('status', 'Active')->first() ?? Year::first();
        $this->semester = Semester::where('status', 'Active')->first() ?? Semester::first();
        
        // Ensure grades_submitable is true for testing
        $this->semester->update(['grades_submitable' => true]);

        $this->section = Section::first();
        $this->course = Course::first();
        $this->instructor = Instructor::first();

        // Setup Instructor User
        $this->instructorUser = $this->instructor->user;
        if (!$this->instructorUser->hasRole('INSTRUCTOR')) {
            $this->instructorUser->assignRole('INSTRUCTOR');
        }
        $this->instructorUser->active_role_id = Role::where('name', 'INSTRUCTOR')->first()->id;
        $this->instructorUser->save();

        // Setup Admin User (admin@sits.edu.et is seeded in TenantSeeder)
        $this->adminUser = User::where('email', 'admin@sits.edu.et')->first() ?? User::factory()->create();
        if (!$this->adminUser->hasRole('ADMIN')) {
            $this->adminUser->assignRole('ADMIN');
        }
        $this->adminUser->active_role_id = Role::where('name', 'ADMIN')->first()->id;
        $this->adminUser->save();

        // Setup Student User and Student
        $this->studentUser = User::factory()->create();
        $this->studentUser->assignRole('STUDENT');
        $this->studentUser->active_role_id = Role::where('name', 'STUDENT')->first()->id;
        $this->studentUser->save();

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'id_no' => 'STU-TEST-99',
            'first_name' => 'Test',
            'middle_name' => 'User',
            'last_name' => 'Student',
            'program_id' => $this->section->program_id,
            'track_id' => $this->section->track_id,
            'study_mode_id' => $this->section->study_mode_id,
            'year_id' => $this->year->id,
            'semester_id' => $this->semester->id,
            'section_id' => $this->section->id,
            'mobile_phone' => '0911000000',
            'office_phone' => '0462000000',
            'date_of_birth' => '2000-01-01',
            'marital_status' => 'Single',
            'sex' => 'M',
            'address' => 'Hawassa, Ethiopia',
            'tenant_id' => 1,
        ]);

        // Create Course Offering
        $this->offering = CourseOffering::create([
            'course_id' => $this->course->id,
            'section_id' => $this->section->id,
            'instructor_id' => $this->instructor->id,
            'year_level' => 1,
            'semester_level' => 1,
            'completed' => 0,
            'grades_submitable' => true,
        ]);
    }

    public function test_weight_point_cap_validation(): void
    {
        $this->actingAs($this->instructorUser);

        // 1. Create a weight under 100 points
        $response = $this->post(route('weights.store'), [
            'name' => 'Mid Exam',
            'point' => 40,
            'description' => 'Mid Term Assessment',
            'course_id' => $this->course->id,
            'section_id' => $this->section->id,
            'semester_id' => $this->semester->id,
            'year_id' => $this->year->id,
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('weights', ['name' => 'Mid Exam', 'point' => 40]);

        // 2. Try to add a weight that exceeds the 100 point cap
        $response = $this->post(route('weights.store'), [
            'name' => 'Final Exam',
            'point' => 70, // Total would be 110
            'description' => 'Final Exam Assessment',
            'course_id' => $this->course->id,
            'section_id' => $this->section->id,
            'semester_id' => $this->semester->id,
            'year_id' => $this->year->id,
        ]);
        $response->assertSessionHasErrors(['point']);
        $this->assertDatabaseMissing('weights', ['name' => 'Final Exam']);

        // 3. Add a weight that is exactly within the 100 point cap
        $response = $this->post(route('weights.store'), [
            'name' => 'Final Exam',
            'point' => 60, // Total exactly 100
            'description' => 'Final Exam Assessment',
            'course_id' => $this->course->id,
            'section_id' => $this->section->id,
            'semester_id' => $this->semester->id,
            'year_id' => $this->year->id,
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('weights', ['name' => 'Final Exam', 'point' => 60]);
    }

    public function test_weight_edit_and_delete_guards(): void
    {
        $this->actingAs($this->instructorUser);

        $weight = Weight::create([
            'name' => 'Quiz 1',
            'point' => 30,
            'course_id' => $this->course->id,
            'section_id' => $this->section->id,
            'semester_id' => $this->semester->id,
            'instructor_id' => $this->instructor->id,
        ]);

        // Guard works: no results yet, should be able to update
        $response = $this->put(route('weights.update', $weight), [
            'name' => 'Quiz 1 Updated',
            'point' => 35,
            'course_id' => $this->course->id,
            'section_id' => $this->section->id,
            'semester_id' => $this->semester->id,
            'year_id' => $this->year->id,
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('weights', ['id' => $weight->id, 'name' => 'Quiz 1 Updated', 'point' => 35]);

        // Add a student result
        $result = \App\Models\Result::create([
            'student_id' => $this->student->id,
            'instructor_id' => $this->instructor->id,
            'weight_id' => $weight->id,
            'point' => 25,
        ]);

        // Update should now be blocked
        $response = $this->put(route('weights.update', $weight), [
            'name' => 'Quiz 1 Tampered',
            'point' => 45,
            'course_id' => $this->course->id,
            'section_id' => $this->section->id,
            'semester_id' => $this->semester->id,
            'year_id' => $this->year->id,
        ]);
        $response->assertSessionHasErrors(['point']);
        $this->assertDatabaseMissing('weights', ['name' => 'Quiz 1 Tampered']);

        // Delete should now be blocked
        $response = $this->delete(route('weights.destroy', $weight));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('weights', ['id' => $weight->id]);

        // Remove the result and delete should succeed
        $result->delete();
        $response = $this->delete(route('weights.destroy', $weight));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('weights', ['id' => $weight->id]);
    }

    public function test_grade_submission_locking_and_complaint_lifecycle(): void
    {
        // 1. Instructor fills the grades (weights sum must be 100)
        $weight = Weight::create([
            'name' => 'Final assessment',
            'point' => 100,
            'course_id' => $this->course->id,
            'section_id' => $this->section->id,
            'semester_id' => $this->semester->id,
            'instructor_id' => $this->instructor->id,
        ]);

        $this->actingAs($this->instructorUser);

        $gradeData = [
            'grades' => [
                [
                    'student_id' => $this->student->id,
                    'grade_point' => '85',
                    'grade_letter' => 'A',
                    'grade_scale' => '100',
                    'grade_status' => 'completed',
                    'year_id' => $this->year->id,
                    'semester_id' => $this->semester->id,
                    'section_id' => $this->section->id,
                    'course_id' => $this->course->id,
                ]
            ]
        ];

        $response = $this->post(route('grades.store'), $gradeData);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('grades', ['student_id' => $this->student->id, 'grade_point' => '85']);

        // Verify initial audit log was written
        $grade = Grade::where('student_id', $this->student->id)->first();
        $this->assertDatabaseHas('grade_audit_logs', [
            'grade_id' => $grade->id,
            'change_type' => 'initial',
            'new_grade_point' => '85',
            'new_grade_letter' => 'A',
        ]);

        // 2. Instructor requests grade submission
        $response = $this->post(route('grade.submission.request', $this->offering));
        $response->assertSessionHasNoErrors();
        $this->offering->refresh();
        $this->assertEquals('pending', $this->offering->grade_submission_status);

        // 3. Admin approves grade submission
        $this->actingAs($this->adminUser);
        $response = $this->post(route('grade.submission.approve', $this->offering), ['notes' => 'Looks good']);
        $response->assertSessionHasNoErrors();

        $this->offering->refresh();
        $grade->refresh();
        $this->assertEquals('approved', $this->offering->grade_submission_status);
        $this->assertTrue((bool)$grade->is_locked);

        // Verify audit log for submission
        $this->assertDatabaseHas('grade_audit_logs', [
            'grade_id' => $grade->id,
            'change_type' => 'submission',
            'new_grade_point' => '85',
        ]);

        // 4. Try to change grade while locked -> should be blocked
        $this->actingAs($this->instructorUser);
        $response = $this->post(route('grades.store'), [
            'grades' => [
                [
                    'student_id' => $this->student->id,
                    'grade_point' => '95',
                    'grade_letter' => 'A+',
                    'grade_scale' => '100',
                    'grade_status' => 'completed',
                    'year_id' => $this->year->id,
                    'semester_id' => $this->semester->id,
                    'section_id' => $this->section->id,
                    'course_id' => $this->course->id,
                ]
            ]
        ]);
        $response->assertSessionHasErrors(['weights']);
        $grade->refresh();
        $this->assertEquals('85', $grade->grade_point); // Unchanged

        // 5. Student files a grade complaint
        $this->actingAs($this->studentUser);
        $response = $this->post(route('grade.complaints.store'), [
            'grade_id' => $grade->id,
            'reason' => 'I deserved a higher score for my final project.',
            'filed_by_role' => 'student',
        ]);
        $response->assertSessionHasNoErrors();

        $complaint = GradeComplaint::where('grade_id', $grade->id)->first();
        $this->assertNotNull($complaint);
        $this->assertEquals('pending', $complaint->status);
        $this->assertEquals('85', $complaint->original_grade_point);

        // 6. Dean (Admin) reviews and approves complaint
        $this->actingAs($this->adminUser);
        $response = $this->post(route('grade.complaints.approve', $complaint), ['review_notes' => 'Approved for recalculation.']);
        $response->assertSessionHasNoErrors();

        $complaint->refresh();
        $grade->refresh();
        $this->assertEquals('approved', $complaint->status);
        $this->assertFalse((bool)$grade->is_locked); // Unlocked for improvement!

        // 7. Instructor submits improved grade
        $this->actingAs($this->instructorUser);
        $response = $this->post(route('grade.complaints.improve', $complaint), [
            'improved_grade_point' => '92',
            'improved_grade_letter' => 'A',
            'notes' => 'Recalculated based on final project details.',
        ]);
        $response->assertSessionHasNoErrors();

        $grade->refresh();
        $complaint->refresh();
        $this->assertTrue((bool)$grade->is_locked); // Re-locked!
        $this->assertEquals('92', $grade->grade_point);
        $this->assertTrue((bool)$complaint->improvement_applied);

        // Verify audit log for improvement
        $this->assertDatabaseHas('grade_audit_logs', [
            'grade_id' => $grade->id,
            'change_type' => 'complaint_improvement',
            'old_grade_point' => '85',
            'new_grade_point' => '92',
        ]);
    }
}
