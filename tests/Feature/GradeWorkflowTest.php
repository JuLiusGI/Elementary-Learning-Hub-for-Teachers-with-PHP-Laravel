<?php

namespace Tests\Feature;

use App\Models\Grade;
use App\Models\GradeLevelSubject;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private User $headTeacher;
    private SchoolYear $schoolYear;
    private Subject $subject;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schoolYear = SchoolYear::factory()->create();
        $this->teacher = User::factory()->teacher('grade_1')->create();
        $this->headTeacher = User::factory()->headTeacher()->create();
        $this->subject = Subject::factory()->create();
        GradeLevelSubject::create([
            'grade_level' => 'grade_1',
            'subject_id' => $this->subject->id,
            'display_order' => 1,
        ]);
        $this->student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $this->schoolYear->id,
            'teacher_id' => $this->teacher->id,
        ]);
    }

    public function test_teacher_can_store_grades(): void
    {
        $response = $this->actingAs($this->teacher)->post('/grades', [
            'subject_id' => $this->subject->id,
            'quarter' => 'Q1',
            'grades' => [
                [
                    'student_id' => $this->student->id,
                    'ww_total_score' => 80,
                    'ww_max_score' => 100,
                    'pt_total_score' => 70,
                    'pt_max_score' => 100,
                    'qa_score' => 90,
                    'qa_max_score' => 100,
                ],
            ],
        ]);

        $response->assertRedirect(route('grades.index'));
        $this->assertDatabaseHas('grades', [
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'quarter' => 'Q1',
            'status' => 'draft',
        ]);
    }

    public function test_teacher_can_submit_draft_grades(): void
    {
        Grade::factory()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'school_year_id' => $this->schoolYear->id,
            'quarter' => 'Q1',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->teacher)->post('/grades/submit', [
            'subject_id' => $this->subject->id,
            'quarter' => 'Q1',
            'grade_level' => 'grade_1',
        ]);

        $response->assertRedirect(route('grades.index'));
        $this->assertDatabaseHas('grades', [
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'status' => 'submitted',
        ]);
    }

    public function test_submit_with_no_draft_grades_fails(): void
    {
        $response = $this->actingAs($this->teacher)->post('/grades/submit', [
            'subject_id' => $this->subject->id,
            'quarter' => 'Q1',
            'grade_level' => 'grade_1',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_head_teacher_can_approve_submitted_grades(): void
    {
        Grade::factory()->submitted()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'school_year_id' => $this->schoolYear->id,
            'quarter' => 'Q1',
        ]);

        $response = $this->actingAs($this->headTeacher)->post(
            "/approvals/grade_1/{$this->subject->id}/Q1/approve"
        );

        $response->assertRedirect(route('approvals.index'));
        $this->assertDatabaseHas('grades', [
            'student_id' => $this->student->id,
            'status' => 'approved',
            'approved_by' => $this->headTeacher->id,
        ]);
    }

    public function test_head_teacher_can_reject_submitted_grades(): void
    {
        Grade::factory()->submitted()->create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'school_year_id' => $this->schoolYear->id,
            'quarter' => 'Q1',
        ]);

        $response = $this->actingAs($this->headTeacher)->post(
            "/approvals/grade_1/{$this->subject->id}/Q1/reject",
            ['reason' => 'Scores need review']
        );

        $response->assertRedirect(route('approvals.index'));
        $this->assertDatabaseHas('grades', [
            'student_id' => $this->student->id,
            'status' => 'draft',
        ]);
    }

    public function test_teacher_cannot_access_approval_routes(): void
    {
        $response = $this->actingAs($this->teacher)->get('/approvals');
        $response->assertStatus(403);

        $response = $this->actingAs($this->teacher)->post(
            "/approvals/grade_1/{$this->subject->id}/Q1/approve"
        );
        $response->assertStatus(403);
    }

    public function test_kinder_teacher_redirected_from_grades(): void
    {
        $kinderTeacher = User::factory()->teacher('kinder')->create();

        $response = $this->actingAs($kinderTeacher)->get('/grades');

        $response->assertRedirect(route('kinder-assessments.index'));
    }

    public function test_grade_calculation_is_correct_on_store(): void
    {
        $this->actingAs($this->teacher)->post('/grades', [
            'subject_id' => $this->subject->id,
            'quarter' => 'Q1',
            'grades' => [
                [
                    'student_id' => $this->student->id,
                    'ww_total_score' => 80,
                    'ww_max_score' => 100,
                    'pt_total_score' => 70,
                    'pt_max_score' => 100,
                    'qa_score' => 90,
                    'qa_max_score' => 100,
                ],
            ],
        ]);

        $grade = Grade::where('student_id', $this->student->id)->first();
        $this->assertEquals('78.00', $grade->quarterly_grade);
        $this->assertEquals('Passed', $grade->remarks);
    }
}
