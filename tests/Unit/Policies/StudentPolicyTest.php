<?php

namespace Tests\Unit\Policies;

use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Policies\StudentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPolicyTest extends TestCase
{
    use RefreshDatabase;

    private StudentPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new StudentPolicy();
    }

    public function test_anyone_can_view_any(): void
    {
        $teacher = User::factory()->teacher()->create();
        $headTeacher = User::factory()->headTeacher()->create();

        $this->assertTrue($this->policy->viewAny($teacher));
        $this->assertTrue($this->policy->viewAny($headTeacher));
    }

    public function test_head_teacher_can_view_any_student(): void
    {
        $headTeacher = User::factory()->headTeacher()->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_3',
            'school_year_id' => $schoolYear->id,
        ]);

        $this->assertTrue($this->policy->view($headTeacher, $student));
    }

    public function test_teacher_can_view_own_grade_level_student(): void
    {
        $teacher = User::factory()->teacher('grade_1')->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $schoolYear->id,
            'teacher_id' => $teacher->id,
        ]);

        $this->assertTrue($this->policy->view($teacher, $student));
    }

    public function test_teacher_cannot_view_other_grade_level_student(): void
    {
        $teacher = User::factory()->teacher('grade_1')->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_2',
            'school_year_id' => $schoolYear->id,
        ]);

        $this->assertFalse($this->policy->view($teacher, $student));
    }

    public function test_anyone_can_create(): void
    {
        $teacher = User::factory()->teacher()->create();
        $this->assertTrue($this->policy->create($teacher));
    }

    public function test_head_teacher_can_update_any_student(): void
    {
        $headTeacher = User::factory()->headTeacher()->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_5',
            'school_year_id' => $schoolYear->id,
        ]);

        $this->assertTrue($this->policy->update($headTeacher, $student));
    }

    public function test_teacher_can_update_own_grade_level_student(): void
    {
        $teacher = User::factory()->teacher('grade_1')->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $schoolYear->id,
            'teacher_id' => $teacher->id,
        ]);

        $this->assertTrue($this->policy->update($teacher, $student));
    }

    public function test_teacher_cannot_update_other_grade_student(): void
    {
        $teacher = User::factory()->teacher('grade_1')->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_2',
            'school_year_id' => $schoolYear->id,
        ]);

        $this->assertFalse($this->policy->update($teacher, $student));
    }

    public function test_only_head_teacher_can_delete(): void
    {
        $headTeacher = User::factory()->headTeacher()->create();
        $teacher = User::factory()->teacher('grade_1')->create();
        $schoolYear = SchoolYear::factory()->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $schoolYear->id,
            'teacher_id' => $teacher->id,
        ]);

        $this->assertTrue($this->policy->delete($headTeacher, $student));
        $this->assertFalse($this->policy->delete($teacher, $student));
    }
}
