<?php

namespace Tests\Unit\Policies;

use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Policies\GradePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradePolicyTest extends TestCase
{
    use RefreshDatabase;

    private GradePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new GradePolicy();
    }

    public function test_anyone_can_view_any(): void
    {
        $teacher = User::factory()->teacher()->create();
        $this->assertTrue($this->policy->viewAny($teacher));
    }

    public function test_head_teacher_can_view_any_grade(): void
    {
        $headTeacher = User::factory()->headTeacher()->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_3',
            'school_year_id' => $schoolYear->id,
        ]);
        $grade = Grade::factory()->create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
        ]);

        $this->assertTrue($this->policy->view($headTeacher, $grade));
    }

    public function test_teacher_can_view_own_grade_level_grades(): void
    {
        $teacher = User::factory()->teacher('grade_1')->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $schoolYear->id,
            'teacher_id' => $teacher->id,
        ]);
        $grade = Grade::factory()->create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
        ]);

        $this->assertTrue($this->policy->view($teacher, $grade));
    }

    public function test_teacher_cannot_view_other_grade_level_grades(): void
    {
        $teacher = User::factory()->teacher('grade_1')->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_2',
            'school_year_id' => $schoolYear->id,
        ]);
        $grade = Grade::factory()->create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
        ]);

        $this->assertFalse($this->policy->view($teacher, $grade));
    }

    public function test_teacher_can_update_draft_own_grade_level(): void
    {
        $teacher = User::factory()->teacher('grade_1')->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $schoolYear->id,
            'teacher_id' => $teacher->id,
        ]);
        $grade = Grade::factory()->create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'status' => 'draft',
        ]);

        $this->assertTrue($this->policy->update($teacher, $grade));
    }

    public function test_teacher_cannot_update_submitted_grade(): void
    {
        $teacher = User::factory()->teacher('grade_1')->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $schoolYear->id,
            'teacher_id' => $teacher->id,
        ]);
        $grade = Grade::factory()->submitted()->create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
        ]);

        $this->assertFalse($this->policy->update($teacher, $grade));
    }

    public function test_only_head_teacher_can_delete_draft_grade(): void
    {
        $headTeacher = User::factory()->headTeacher()->create();
        $teacher = User::factory()->teacher('grade_1')->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $schoolYear->id,
            'teacher_id' => $teacher->id,
        ]);
        $grade = Grade::factory()->create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'status' => 'draft',
        ]);

        $this->assertTrue($this->policy->delete($headTeacher, $grade));
        $this->assertFalse($this->policy->delete($teacher, $grade));
    }

    public function test_cannot_delete_non_draft_grade(): void
    {
        $headTeacher = User::factory()->headTeacher()->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $schoolYear->id,
        ]);
        $grade = Grade::factory()->submitted()->create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
        ]);

        $this->assertFalse($this->policy->delete($headTeacher, $grade));
    }

    public function test_only_head_teacher_can_approve(): void
    {
        $headTeacher = User::factory()->headTeacher()->create();
        $teacher = User::factory()->teacher()->create();

        $this->assertTrue($this->policy->approve($headTeacher));
        $this->assertFalse($this->policy->approve($teacher));
    }
}
