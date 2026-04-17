<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\StoreAssignmentScoresRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssignmentScore;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Assignment::class, 'assignment');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $gradeLevel = $user->isTeacher() ? $user->grade_level : $request->get('grade_level');

        $query = Assignment::with(['subject', 'teacher'])
            ->withCount('scores')
            ->currentSchoolYear()
            ->orderByDesc('created_at');

        if ($gradeLevel) {
            $query->forGradeLevel($gradeLevel);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('quarter')) {
            $query->forQuarter($request->quarter);
        }
        if ($request->filled('type')) {
            $query->forType($request->type);
        }

        $assignments = $query->paginate(20)->withQueryString();

        $subjects = Subject::active()
            ->when($gradeLevel, fn ($q) => $q->forGradeLevel($gradeLevel))
            ->orderBy('display_order')
            ->get();

        return view('assignments.index', compact('assignments', 'subjects', 'gradeLevel'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $gradeLevel = $user->isTeacher() ? $user->grade_level : $request->get('grade_level', 'grade_1');

        $subjects = Subject::active()
            ->forGradeLevel($gradeLevel)
            ->orderBy('display_order')
            ->get();

        return view('assignments.create', compact('subjects', 'gradeLevel'));
    }

    public function store(StoreAssignmentRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $data['teacher_id'] = $user->id;
        $data['school_year_id'] = SchoolYear::current()->first()->id;

        if ($user->isTeacher()) {
            $data['grade_level'] = $user->grade_level;
        }

        Assignment::create($data);

        return redirect()->route('assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    public function show(Assignment $assignment)
    {
        $assignment->load(['subject', 'teacher', 'scores.student']);

        $totalStudents = Student::currentSchoolYear()
            ->active()
            ->forGradeLevel($assignment->grade_level)
            ->count();

        return view('assignments.show', compact('assignment', 'totalStudents'));
    }

    public function edit(Assignment $assignment)
    {
        $subjects = Subject::active()
            ->forGradeLevel($assignment->grade_level)
            ->orderBy('display_order')
            ->get();

        return view('assignments.edit', compact('assignment', 'subjects'));
    }

    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        $assignment->update($request->validated());

        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }

    public function scores(Request $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $students = Student::currentSchoolYear()
            ->active()
            ->forGradeLevel($assignment->grade_level)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $existingScores = $assignment->scores()->get()->keyBy('student_id');

        return view('assignments.scores', compact('assignment', 'students', 'existingScores'));
    }

    public function storeScores(StoreAssignmentScoresRequest $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $data = $request->validated();

        foreach ($data['scores'] as $entry) {
            if ($entry['score'] === null && empty($entry['remarks'])) {
                continue;
            }

            AssignmentScore::updateOrCreate(
                [
                    'assignment_id' => $assignment->id,
                    'student_id' => $entry['student_id'],
                ],
                [
                    'score' => $entry['score'],
                    'remarks' => $entry['remarks'] ?? null,
                ]
            );
        }

        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Scores saved successfully.');
    }
}
