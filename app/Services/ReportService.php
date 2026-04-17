<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Grade;
use App\Models\KinderAssessment;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(
        protected GradeCalculatorService $calculator
    ) {}

    public function schoolInfo(): array
    {
        return [
            'name' => config('school.name'),
            'lrn_id' => config('school.lrn_id'),
            'address' => config('school.address'),
            'region' => config('school.region'),
        ];
    }

    public function getSubjectsForGradeLevel(string $gradeLevel): Collection
    {
        return Subject::active()
            ->forGradeLevel($gradeLevel)
            ->orderBy('display_order')
            ->get();
    }

    public function getStudentSf9Data(Student $student, SchoolYear $schoolYear): array
    {
        $subjects = $this->getSubjectsForGradeLevel($student->grade_level);

        $grades = Grade::where('student_id', $student->id)
            ->where('school_year_id', $schoolYear->id)
            ->whereIn('status', ['approved', 'locked'])
            ->get()
            ->groupBy('subject_id');

        $subjectGrades = [];
        $allFinalGrades = [];

        foreach ($subjects as $subject) {
            $subjectData = ['subject' => $subject, 'quarters' => [], 'final_grade' => null, 'remarks' => null];
            $quarterlyValues = [];

            foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) {
                $grade = ($grades->get($subject->id) ?? collect())->firstWhere('quarter', $quarter);
                $subjectData['quarters'][$quarter] = $grade?->quarterly_grade;
                if ($grade?->quarterly_grade !== null) {
                    $quarterlyValues[] = (float) $grade->quarterly_grade;
                }
            }

            if (count($quarterlyValues) === 4) {
                $finalGrade = $this->calculator->calculateFinalGrade($quarterlyValues);
                $subjectData['final_grade'] = $finalGrade;
                $subjectData['remarks'] = $this->calculator->getFinalRemarks($finalGrade);
                $allFinalGrades[] = $finalGrade;
            }

            $subjectGrades[] = $subjectData;
        }

        $generalAverage = count($allFinalGrades) === $subjects->count() && $subjects->count() > 0
            ? round(array_sum($allFinalGrades) / count($allFinalGrades), 2)
            : null;

        $attendance = $this->getAttendanceSummaryForSf9($student, $schoolYear);

        return [
            'student' => $student,
            'schoolYear' => $schoolYear,
            'school' => $this->schoolInfo(),
            'subjectGrades' => $subjectGrades,
            'generalAverage' => $generalAverage,
            'generalRemarks' => $generalAverage !== null ? $this->calculator->getFinalRemarks($generalAverage) : null,
            'attendance' => $attendance,
            'teacher' => $student->teacher,
        ];
    }

    public function getStudentSf9KinderData(Student $student, SchoolYear $schoolYear): array
    {
        $domains = config('school.kinder_domains');

        $assessments = KinderAssessment::where('student_id', $student->id)
            ->where('school_year_id', $schoolYear->id)
            ->whereIn('status', ['approved', 'locked'])
            ->get();

        $domainAssessments = [];
        foreach ($domains as $key => $label) {
            $domainData = ['domain' => $label, 'quarters' => []];
            foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) {
                $assessment = $assessments->where('domain', $key)->where('quarter', $quarter)->first();
                $domainData['quarters'][$quarter] = $assessment?->rating_label;
            }
            $domainAssessments[] = $domainData;
        }

        $attendance = $this->getAttendanceSummaryForSf9($student, $schoolYear);

        return [
            'student' => $student,
            'schoolYear' => $schoolYear,
            'school' => $this->schoolInfo(),
            'domainAssessments' => $domainAssessments,
            'attendance' => $attendance,
            'teacher' => $student->teacher,
            'isKinder' => true,
        ];
    }

    public function getAttendanceSummaryForSf9(Student $student, SchoolYear $schoolYear): array
    {
        $startYear = $schoolYear->start_date->year;
        $months = [
            ['year' => $startYear, 'month' => 6, 'label' => 'June'],
            ['year' => $startYear, 'month' => 7, 'label' => 'July'],
            ['year' => $startYear, 'month' => 8, 'label' => 'August'],
            ['year' => $startYear, 'month' => 9, 'label' => 'September'],
            ['year' => $startYear, 'month' => 10, 'label' => 'October'],
            ['year' => $startYear, 'month' => 11, 'label' => 'November'],
            ['year' => $startYear, 'month' => 12, 'label' => 'December'],
            ['year' => $startYear + 1, 'month' => 1, 'label' => 'January'],
            ['year' => $startYear + 1, 'month' => 2, 'label' => 'February'],
            ['year' => $startYear + 1, 'month' => 3, 'label' => 'March'],
        ];

        $allRecords = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$schoolYear->start_date, $schoolYear->end_date])
            ->get();

        $summary = [];
        foreach ($months as $m) {
            $monthRecords = $allRecords->filter(
                fn ($r) => $r->date->year === $m['year'] && $r->date->month === $m['month']
            );
            $summary[] = [
                'label' => $m['label'],
                'present' => $monthRecords->whereIn('status', ['present', 'late'])->count(),
                'absent' => $monthRecords->where('status', 'absent')->count(),
                'tardy' => $monthRecords->where('status', 'late')->count(),
            ];
        }

        return $summary;
    }

    public function getStudentSf10Data(Student $student): array
    {
        $schoolYears = SchoolYear::whereHas('students', fn ($q) => $q->where('students.id', $student->id))
            ->orderBy('start_date')
            ->get();

        $scholasticRecords = [];
        foreach ($schoolYears as $sy) {
            $gradeLevel = $student->grade_level;

            if ($gradeLevel === 'kinder') {
                $scholasticRecords[] = $this->getKinderScholasticRecord($student, $sy);
                continue;
            }

            $subjects = $this->getSubjectsForGradeLevel($gradeLevel);
            $grades = Grade::where('student_id', $student->id)
                ->where('school_year_id', $sy->id)
                ->whereIn('status', ['approved', 'locked'])
                ->get()
                ->groupBy('subject_id');

            $subjectGrades = [];
            $allFinalGrades = [];

            foreach ($subjects as $subject) {
                $subjectData = ['subject' => $subject, 'quarters' => [], 'final_grade' => null, 'remarks' => null];
                $quarterlyValues = [];

                foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) {
                    $grade = ($grades->get($subject->id) ?? collect())->firstWhere('quarter', $quarter);
                    $subjectData['quarters'][$quarter] = $grade?->quarterly_grade;
                    if ($grade?->quarterly_grade !== null) {
                        $quarterlyValues[] = (float) $grade->quarterly_grade;
                    }
                }

                if (count($quarterlyValues) === 4) {
                    $finalGrade = $this->calculator->calculateFinalGrade($quarterlyValues);
                    $subjectData['final_grade'] = $finalGrade;
                    $subjectData['remarks'] = $this->calculator->getFinalRemarks($finalGrade);
                    $allFinalGrades[] = $finalGrade;
                }

                $subjectGrades[] = $subjectData;
            }

            $generalAverage = count($allFinalGrades) === $subjects->count() && $subjects->count() > 0
                ? round(array_sum($allFinalGrades) / count($allFinalGrades), 2)
                : null;

            $scholasticRecords[] = [
                'school_year' => $sy,
                'grade_level' => config('school.grade_levels')[$gradeLevel] ?? $gradeLevel,
                'subjects' => $subjectGrades,
                'general_average' => $generalAverage,
                'remarks' => $generalAverage !== null ? $this->calculator->getFinalRemarks($generalAverage) : null,
                'is_kinder' => false,
            ];
        }

        return [
            'student' => $student,
            'school' => $this->schoolInfo(),
            'scholasticRecords' => $scholasticRecords,
        ];
    }

    protected function getKinderScholasticRecord(Student $student, SchoolYear $sy): array
    {
        $domains = config('school.kinder_domains');
        $assessments = KinderAssessment::where('student_id', $student->id)
            ->where('school_year_id', $sy->id)
            ->whereIn('status', ['approved', 'locked'])
            ->get();

        $domainAssessments = [];
        foreach ($domains as $key => $label) {
            $domainData = ['domain' => $label, 'quarters' => []];
            foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) {
                $assessment = $assessments->where('domain', $key)->where('quarter', $quarter)->first();
                $domainData['quarters'][$quarter] = $assessment?->rating_label;
            }
            $domainAssessments[] = $domainData;
        }

        return [
            'school_year' => $sy,
            'grade_level' => 'Kindergarten',
            'domains' => $domainAssessments,
            'general_average' => null,
            'remarks' => null,
            'is_kinder' => true,
        ];
    }

    public function getClassGradeSummary(string $gradeLevel, SchoolYear $schoolYear, string $quarter): array
    {
        $subjects = $this->getSubjectsForGradeLevel($gradeLevel);
        $students = Student::currentSchoolYear()->active()
            ->forGradeLevel($gradeLevel)
            ->orderBy('last_name')->orderBy('first_name')
            ->get();

        $allGrades = Grade::whereIn('student_id', $students->pluck('id'))
            ->where('school_year_id', $schoolYear->id)
            ->where('quarter', $quarter)
            ->whereIn('status', ['approved', 'locked'])
            ->get();

        $studentRows = [];
        foreach ($students as $student) {
            $row = ['student' => $student, 'grades' => [], 'average' => null];
            $gradeValues = [];

            foreach ($subjects as $subject) {
                $grade = $allGrades->where('student_id', $student->id)->where('subject_id', $subject->id)->first();
                $row['grades'][$subject->id] = $grade?->quarterly_grade;
                if ($grade?->quarterly_grade !== null) {
                    $gradeValues[] = (float) $grade->quarterly_grade;
                }
            }

            $row['average'] = count($gradeValues) > 0
                ? round(array_sum($gradeValues) / count($gradeValues), 2)
                : null;
            $studentRows[] = $row;
        }

        return [
            'school' => $this->schoolInfo(),
            'gradeLevel' => config('school.grade_levels')[$gradeLevel] ?? $gradeLevel,
            'gradeLevelKey' => $gradeLevel,
            'quarter' => $quarter,
            'schoolYear' => $schoolYear,
            'subjects' => $subjects,
            'studentRows' => $studentRows,
        ];
    }

    public function getMonthlyAttendanceReport(string $gradeLevel, int $year, int $month): array
    {
        $students = Student::currentSchoolYear()->active()
            ->forGradeLevel($gradeLevel)
            ->orderBy('last_name')->orderBy('first_name')
            ->get();

        $records = Attendance::whereIn('student_id', $students->pluck('id'))
            ->forMonth($year, $month)
            ->get();

        $studentRows = $students->map(function ($student) use ($records) {
            $studentRecords = $records->where('student_id', $student->id);
            return [
                'student' => $student,
                'present' => $studentRecords->whereIn('status', ['present', 'late'])->count(),
                'absent' => $studentRecords->where('status', 'absent')->count(),
                'late' => $studentRecords->where('status', 'late')->count(),
                'excused' => $studentRecords->where('status', 'excused')->count(),
                'total' => $studentRecords->count(),
            ];
        });

        return [
            'school' => $this->schoolInfo(),
            'gradeLevel' => config('school.grade_levels')[$gradeLevel] ?? $gradeLevel,
            'gradeLevelKey' => $gradeLevel,
            'year' => $year,
            'month' => $month,
            'monthName' => \Carbon\Carbon::create($year, $month)->format('F Y'),
            'studentRows' => $studentRows,
        ];
    }
}
