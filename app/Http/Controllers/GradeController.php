<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGradeRequest;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use App\Services\GradeCalculatorService;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function __construct(
        private GradeCalculatorService $calculator
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        // Kinder teachers go to kinder assessments
        if ($user->isTeacher() && $user->grade_level === 'kinder') {
            return redirect()->route('kinder-assessments.index');
        }

        $gradeLevel = $user->isTeacher() ? $user->grade_level : $request->get('grade_level', 'grade_1');

        $subjects = Subject::active()
            ->forGradeLevel($gradeLevel)
            ->orderBy('display_order')
            ->get();

        $schoolYear = SchoolYear::current()->first();

        // Get grade statuses for each subject/quarter combo
        $gradeStatuses = [];
        if ($schoolYear) {
            $grades = Grade::where('school_year_id', $schoolYear->id)
                ->forGradeLevel($gradeLevel)
                ->get();

            foreach ($subjects as $subject) {
                foreach (config('school.quarters') as $quarter) {
                    $subjectGrades = $grades->where('subject_id', $subject->id)->where('quarter', $quarter);
                    if ($subjectGrades->isEmpty()) {
                        $gradeStatuses[$subject->id][$quarter] = null;
                    } else {
                        // Use the most common status
                        $gradeStatuses[$subject->id][$quarter] = $subjectGrades->groupBy('status')->sortByDesc(fn ($g) => $g->count())->keys()->first();
                    }
                }
            }
        }

        return view('grades.index', compact('subjects', 'gradeLevel', 'gradeStatuses'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $subjectId = $request->get('subject_id');
        $quarter = $request->get('quarter', 'Q1');

        $subject = Subject::findOrFail($subjectId);
        $gradeLevel = $user->isTeacher() ? $user->grade_level : $request->get('grade_level');

        $students = Student::currentSchoolYear()
            ->active()
            ->forGradeLevel($gradeLevel)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $schoolYear = SchoolYear::current()->first();

        // Load existing grades
        $existingGrades = Grade::where('school_year_id', $schoolYear->id)
            ->where('subject_id', $subjectId)
            ->where('quarter', $quarter)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // Check if grades are editable
        $isLocked = $existingGrades->contains(fn ($g) => $g->isLocked() || $g->isApproved());
        $isSubmitted = $existingGrades->contains(fn ($g) => $g->isSubmitted());

        return view('grades.create', compact('subject', 'quarter', 'gradeLevel', 'students', 'existingGrades', 'isLocked', 'isSubmitted'));
    }

    public function store(StoreGradeRequest $request)
    {
        $data = $request->validated();
        $schoolYear = SchoolYear::current()->first();

        foreach ($data['grades'] as $entry) {
            // Skip empty entries
            if (empty($entry['ww_total_score']) && empty($entry['pt_total_score']) && empty($entry['qa_score'])) {
                continue;
            }

            $calculated = $this->calculator->calculate(
                $entry['ww_total_score'] ?? null,
                $entry['ww_max_score'] ?? null,
                $entry['pt_total_score'] ?? null,
                $entry['pt_max_score'] ?? null,
                $entry['qa_score'] ?? null,
                $entry['qa_max_score'] ?? null,
            );

            Grade::updateOrCreate(
                [
                    'student_id' => $entry['student_id'],
                    'subject_id' => $data['subject_id'],
                    'school_year_id' => $schoolYear->id,
                    'quarter' => $data['quarter'],
                ],
                array_merge([
                    'ww_total_score' => $entry['ww_total_score'] ?? null,
                    'ww_max_score' => $entry['ww_max_score'] ?? null,
                    'pt_total_score' => $entry['pt_total_score'] ?? null,
                    'pt_max_score' => $entry['pt_max_score'] ?? null,
                    'qa_score' => $entry['qa_score'] ?? null,
                    'qa_max_score' => $entry['qa_max_score'] ?? null,
                    'status' => 'draft',
                ], $calculated)
            );
        }

        return redirect()->route('grades.index')
            ->with('success', 'Grades saved as draft.');
    }

    public function show(Request $request, Student $student)
    {
        $user = $request->user();

        if ($user->isTeacher() && $user->grade_level !== $student->grade_level) {
            abort(403);
        }

        $schoolYear = SchoolYear::current()->first();
        $subjects = Subject::active()
            ->forGradeLevel($student->grade_level)
            ->orderBy('display_order')
            ->get();

        $grades = Grade::where('student_id', $student->id)
            ->where('school_year_id', $schoolYear->id)
            ->get()
            ->groupBy('subject_id');

        // Calculate final grades per subject
        $finalGrades = [];
        foreach ($subjects as $subject) {
            $subjectGrades = $grades->get($subject->id, collect());
            $quarterlyGrades = [];
            foreach (config('school.quarters') as $quarter) {
                $qGrade = $subjectGrades->firstWhere('quarter', $quarter);
                if ($qGrade && $qGrade->quarterly_grade !== null) {
                    $quarterlyGrades[] = (float) $qGrade->quarterly_grade;
                }
            }
            $finalGrade = $this->calculator->calculateFinalGrade($quarterlyGrades);
            $finalGrades[$subject->id] = [
                'grade' => $finalGrade,
                'remarks' => $finalGrade !== null ? $this->calculator->getFinalRemarks($finalGrade) : null,
            ];
        }

        return view('grades.show', compact('student', 'subjects', 'grades', 'finalGrades'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'quarter' => 'required|in:Q1,Q2,Q3,Q4',
            'grade_level' => 'required',
        ]);

        $user = $request->user();
        $schoolYear = SchoolYear::current()->first();

        $grades = Grade::where('school_year_id', $schoolYear->id)
            ->where('subject_id', $request->subject_id)
            ->where('quarter', $request->quarter)
            ->forGradeLevel($request->grade_level)
            ->forStatus('draft')
            ->get();

        if ($grades->isEmpty()) {
            return back()->with('error', 'No draft grades to submit.');
        }

        foreach ($grades as $grade) {
            $grade->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
        }

        return redirect()->route('grades.index')
            ->with('success', 'Grades submitted for approval.');
    }
}
