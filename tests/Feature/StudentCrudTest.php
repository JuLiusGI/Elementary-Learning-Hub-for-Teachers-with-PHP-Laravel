<?php

namespace Tests\Feature;

use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private User $headTeacher;
    private SchoolYear $schoolYear;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schoolYear = SchoolYear::factory()->create();
        $this->teacher = User::factory()->teacher('grade_1')->create();
        $this->headTeacher = User::factory()->headTeacher()->create();
    }

    public function test_teacher_can_view_student_index(): void
    {
        $student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $this->schoolYear->id,
            'teacher_id' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->teacher)->get('/students');

        $response->assertStatus(200);
        $response->assertSee($student->first_name);
    }

    public function test_teacher_cannot_see_other_grade_level_students(): void
    {
        $otherTeacher = User::factory()->teacher('grade_2')->create();
        $otherStudent = Student::factory()->create([
            'grade_level' => 'grade_2',
            'school_year_id' => $this->schoolYear->id,
            'teacher_id' => $otherTeacher->id,
        ]);

        $response = $this->actingAs($this->teacher)->get('/students');

        $response->assertStatus(200);
        $response->assertDontSee($otherStudent->first_name);
    }

    public function test_head_teacher_can_view_all_students(): void
    {
        $student1 = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $this->schoolYear->id,
            'teacher_id' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->headTeacher)->get('/students');

        $response->assertStatus(200);
    }

    public function test_teacher_can_create_student(): void
    {
        $response = $this->actingAs($this->teacher)->post('/students', [
            'lrn' => '123456789012',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'date_of_birth' => '2018-05-15',
            'gender' => 'male',
            'address_barangay' => 'Sample Barangay',
            'address_municipality' => 'Sample Municipality',
            'address_province' => 'Sample Province',
            'guardian_name' => 'Maria Dela Cruz',
            'guardian_contact' => '09123456789',
            'guardian_relationship' => 'mother',
            'grade_level' => 'grade_1',
            'enrollment_status' => 'active',
            'date_enrolled' => '2026-06-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('students', [
            'lrn' => '123456789012',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
        ]);
    }

    public function test_duplicate_lrn_fails_validation(): void
    {
        Student::factory()->create([
            'lrn' => '123456789012',
            'school_year_id' => $this->schoolYear->id,
            'teacher_id' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->teacher)->post('/students', [
            'lrn' => '123456789012',
            'first_name' => 'Another',
            'last_name' => 'Student',
            'date_of_birth' => '2018-05-15',
            'gender' => 'male',
            'address_barangay' => 'Sample Barangay',
            'address_municipality' => 'Sample Municipality',
            'address_province' => 'Sample Province',
            'guardian_name' => 'Guardian',
            'guardian_contact' => '09123456789',
            'guardian_relationship' => 'mother',
            'grade_level' => 'grade_1',
            'enrollment_status' => 'active',
            'date_enrolled' => '2026-06-01',
        ]);

        $response->assertSessionHasErrors('lrn');
    }

    public function test_head_teacher_can_delete_student(): void
    {
        $student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $this->schoolYear->id,
            'teacher_id' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->headTeacher)->delete("/students/{$student->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_teacher_cannot_delete_student(): void
    {
        $student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $this->schoolYear->id,
            'teacher_id' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->teacher)->delete("/students/{$student->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('students', ['id' => $student->id]);
    }

    public function test_unauthenticated_user_cannot_access_students(): void
    {
        $response = $this->get('/students');

        $response->assertRedirect('/login');
    }

    public function test_teacher_can_view_own_grade_student(): void
    {
        $student = Student::factory()->create([
            'grade_level' => 'grade_1',
            'school_year_id' => $this->schoolYear->id,
            'teacher_id' => $this->teacher->id,
        ]);

        $response = $this->actingAs($this->teacher)->get("/students/{$student->id}");

        $response->assertStatus(200);
    }

    public function test_teacher_cannot_view_other_grade_student(): void
    {
        $otherTeacher = User::factory()->teacher('grade_2')->create();
        $student = Student::factory()->create([
            'grade_level' => 'grade_2',
            'school_year_id' => $this->schoolYear->id,
            'teacher_id' => $otherTeacher->id,
        ]);

        $response = $this->actingAs($this->teacher)->get("/students/{$student->id}");

        $response->assertStatus(403);
    }
}
