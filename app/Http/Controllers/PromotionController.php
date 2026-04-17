<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentPromotion;
use App\Services\PromotionService;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function __construct(
        protected PromotionService $promotionService
    ) {}

    public function index(Request $request)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);

        $schoolYears = SchoolYear::orderByDesc('start_date')->get();
        $selectedSchoolYear = $request->filled('school_year_id')
            ? SchoolYear::findOrFail($request->school_year_id)
            : SchoolYear::current()->first();

        $candidates = $selectedSchoolYear
            ? $this->promotionService->getPromotionCandidates($selectedSchoolYear)
            : [];

        return view('promotions.index', compact('schoolYears', 'selectedSchoolYear', 'candidates'));
    }

    public function review(Request $request)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);

        $request->validate([
            'school_year_id' => ['required', 'exists:school_years,id'],
            'grade_level' => ['required', 'in:' . implode(',', array_keys(config('school.grade_levels')))],
        ]);

        $schoolYear = SchoolYear::findOrFail($request->school_year_id);
        $gradeLevel = $request->grade_level;

        $candidates = $this->promotionService->getPromotionCandidates($schoolYear);
        $gradeLevelCandidates = $candidates[$gradeLevel] ?? [];

        $nextSchoolYears = SchoolYear::where('id', '!=', $schoolYear->id)
            ->active()
            ->orderByDesc('start_date')
            ->get();

        return view('promotions.review', compact(
            'schoolYear', 'gradeLevel', 'gradeLevelCandidates', 'nextSchoolYears'
        ));
    }

    public function process(Request $request)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);

        $request->validate([
            'from_school_year_id' => ['required', 'exists:school_years,id'],
            'to_school_year_id' => ['required', 'exists:school_years,id'],
            'decisions' => ['required', 'array'],
            'decisions.*.student_id' => ['required', 'exists:students,id'],
            'decisions.*.status' => ['required', 'in:promoted,retained,graduated'],
            'decisions.*.remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $fromSchoolYear = SchoolYear::findOrFail($request->from_school_year_id);
        $toSchoolYear = SchoolYear::findOrFail($request->to_school_year_id);

        $processed = 0;
        foreach ($request->decisions as $decision) {
            $student = Student::findOrFail($decision['student_id']);

            // Skip if already promoted
            $existing = StudentPromotion::where('student_id', $student->id)
                ->where('from_school_year_id', $fromSchoolYear->id)
                ->first();
            if ($existing) {
                continue;
            }

            $toGradeLevel = $decision['status'] === 'retained'
                ? $student->grade_level
                : StudentPromotion::nextGradeLevel($student->grade_level);

            $generalAverage = $this->promotionService->getPromotionCandidates($fromSchoolYear)[$student->grade_level] ?? [];
            $candidateData = collect($generalAverage)->firstWhere('student.id', $student->id);
            $avg = $candidateData['general_average'] ?? null;

            $this->promotionService->promoteStudent(
                $student,
                $fromSchoolYear,
                $toSchoolYear,
                $decision['status'],
                $toGradeLevel,
                $avg,
                $decision['remarks'] ?? null
            );

            $processed++;
        }

        return redirect()->route('promotions.index', ['school_year_id' => $fromSchoolYear->id])
            ->with('success', "{$processed} student(s) processed successfully.");
    }

    public function history(Request $request)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);

        $schoolYears = SchoolYear::orderByDesc('start_date')->get();

        $query = StudentPromotion::with(['student', 'fromSchoolYear', 'toSchoolYear', 'decidedBy'])
            ->orderByDesc('promoted_at');

        if ($request->filled('school_year_id')) {
            $query->where('from_school_year_id', $request->school_year_id);
        }

        $promotions = $query->paginate(20)->withQueryString();

        return view('promotions.history', compact('schoolYears', 'promotions'));
    }
}
