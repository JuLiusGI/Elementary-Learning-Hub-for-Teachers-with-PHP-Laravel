<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\KinderAssessment;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentPromotion;
use App\Models\Subject;

class PromotionService
{
    public function __construct(
        protected GradeCalculatorService $calculator,
        protected AuditLogService $auditLog
    ) {}

    public function getPromotionCandidates(SchoolYear $schoolYear): array
    {
        $students = Student::where('school_year_id', $schoolYear->id)
            ->active()
            ->orderBy('grade_level')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $grouped = [];

        foreach ($students as $student) {
            $gradeLevel = $student->grade_level;

            $generalAverage = $this->calculateGeneralAverage($student, $schoolYear);
            $recommendation = $this->getRecommendation($student, $generalAverage);

            $existingPromotion = StudentPromotion::where('student_id', $student->id)
                ->where('from_school_year_id', $schoolYear->id)
                ->first();

            $grouped[$gradeLevel][] = [
                'student' => $student,
                'general_average' => $generalAverage,
                'recommendation' => $recommendation,
                'next_grade_level' => StudentPromotion::nextGradeLevel($gradeLevel),
                'promotion' => $existingPromotion,
            ];
        }

        return $grouped;
    }

    public function promoteStudent(
        Student $student,
        SchoolYear $fromSchoolYear,
        SchoolYear $toSchoolYear,
        string $status,
        string $toGradeLevel,
        ?float $generalAverage,
        ?string $remarks
    ): StudentPromotion {
        $promotion = StudentPromotion::create([
            'student_id' => $student->id,
            'from_school_year_id' => $fromSchoolYear->id,
            'to_school_year_id' => $toSchoolYear->id,
            'from_grade_level' => $student->grade_level,
            'to_grade_level' => $toGradeLevel,
            'general_average' => $generalAverage,
            'status' => $status,
            'decision_by' => auth()->id(),
            'remarks' => $remarks,
            'promoted_at' => now(),
        ]);

        if ($status === 'graduated') {
            $student->update(['enrollment_status' => 'graduated']);
        }

        $this->auditLog->log('student.promoted', $promotion, null, [
            'student_name' => $student->full_name,
            'from' => $student->grade_level,
            'to' => $toGradeLevel,
            'status' => $status,
        ]);

        return $promotion;
    }

    protected function calculateGeneralAverage(Student $student, SchoolYear $schoolYear): ?float
    {
        if ($student->grade_level === 'kinder') {
            return null; // Kinder uses qualitative ratings, no numerical average
        }

        $subjects = Subject::active()
            ->forGradeLevel($student->grade_level)
            ->get();

        $grades = Grade::where('student_id', $student->id)
            ->where('school_year_id', $schoolYear->id)
            ->whereIn('status', ['approved', 'locked'])
            ->get()
            ->groupBy('subject_id');

        $allFinalGrades = [];

        foreach ($subjects as $subject) {
            $quarterlyValues = [];
            foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) {
                $grade = ($grades->get($subject->id) ?? collect())->firstWhere('quarter', $quarter);
                if ($grade?->quarterly_grade !== null) {
                    $quarterlyValues[] = (float) $grade->quarterly_grade;
                }
            }

            if (count($quarterlyValues) === 4) {
                $finalGrade = $this->calculator->calculateFinalGrade($quarterlyValues);
                if ($finalGrade !== null) {
                    $allFinalGrades[] = $finalGrade;
                }
            }
        }

        if (count($allFinalGrades) === $subjects->count() && $subjects->count() > 0) {
            return round(array_sum($allFinalGrades) / count($allFinalGrades), 2);
        }

        return null;
    }

    protected function getRecommendation(Student $student, ?float $generalAverage): string
    {
        if ($student->grade_level === 'kinder') {
            return 'promoted'; // Kinder students are typically promoted
        }

        if ($generalAverage === null) {
            return 'pending'; // Not enough grades to determine
        }

        $passingGrade = config('school.grading.passing_grade');

        if ($student->grade_level === 'grade_6' && $generalAverage >= $passingGrade) {
            return 'graduated';
        }

        return $generalAverage >= $passingGrade ? 'promoted' : 'retained';
    }
}
