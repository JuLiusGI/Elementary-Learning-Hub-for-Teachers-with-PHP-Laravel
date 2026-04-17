<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\KinderAssessment;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class GradeApprovalController extends Controller
{
    public function __construct(private AuditLogService $auditLog)
    {
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->isHeadTeacher(), 403);
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $schoolYear = SchoolYear::current()->first();
        $statusFilter = $request->get('status', 'submitted');

        // Get grade submissions grouped by grade_level + subject + quarter
        $gradeSubmissions = collect();
        if ($schoolYear) {
            $grades = Grade::with(['student', 'subject'])
                ->where('school_year_id', $schoolYear->id)
                ->where('status', $statusFilter)
                ->get();

            $gradeSubmissions = $grades->groupBy(function ($g) {
                return $g->student->grade_level . '|' . $g->subject_id . '|' . $g->quarter;
            })->map(function ($group) {
                $first = $group->first();
                return [
                    'type' => 'grade',
                    'grade_level' => $first->student->grade_level,
                    'grade_level_label' => config('school.grade_levels')[$first->student->grade_level] ?? $first->student->grade_level,
                    'subject' => $first->subject->name,
                    'subject_id' => $first->subject_id,
                    'quarter' => $first->quarter,
                    'student_count' => $group->count(),
                    'status' => $first->status,
                    'submitted_at' => $first->submitted_at,
                    'approved_at' => $first->approved_at,
                ];
            })->values();
        }

        // Get kinder submissions
        $kinderSubmissions = collect();
        if ($schoolYear) {
            $kinderRecords = KinderAssessment::with('student')
                ->where('school_year_id', $schoolYear->id)
                ->where('status', $statusFilter)
                ->get();

            if ($kinderRecords->isNotEmpty()) {
                $kinderSubmissions = $kinderRecords->groupBy('quarter')->map(function ($group) {
                    $first = $group->first();
                    return [
                        'type' => 'kinder',
                        'grade_level' => 'kinder',
                        'grade_level_label' => 'Kindergarten',
                        'subject' => 'Developmental Domains',
                        'subject_id' => null,
                        'quarter' => $first->quarter,
                        'student_count' => $group->unique('student_id')->count(),
                        'status' => $first->status,
                        'submitted_at' => $first->submitted_at,
                        'approved_at' => $first->approved_at,
                    ];
                })->values();
            }
        }

        $submissions = $gradeSubmissions->concat($kinderSubmissions)->sortBy('grade_level');

        return view('approvals.index', compact('submissions', 'statusFilter'));
    }

    public function show(Request $request, string $gradeLevel, ?string $subjectId, string $quarter)
    {
        $schoolYear = SchoolYear::current()->first();

        if ($gradeLevel === 'kinder') {
            $assessments = KinderAssessment::with('student')
                ->where('school_year_id', $schoolYear->id)
                ->where('quarter', $quarter)
                ->where('status', 'submitted')
                ->get()
                ->groupBy('student_id');

            $domains = config('school.kinder_domains');

            return view('approvals.show', compact('gradeLevel', 'quarter', 'assessments', 'domains'));
        }

        $subject = Subject::findOrFail($subjectId);
        $grades = Grade::with('student')
            ->where('school_year_id', $schoolYear->id)
            ->where('subject_id', $subjectId)
            ->where('quarter', $quarter)
            ->forGradeLevel($gradeLevel)
            ->where('status', 'submitted')
            ->orderBy('student_id')
            ->get();

        return view('approvals.show', compact('gradeLevel', 'quarter', 'subject', 'grades'));
    }

    public function approve(Request $request, string $gradeLevel, ?string $subjectId, string $quarter)
    {
        $schoolYear = SchoolYear::current()->first();

        if ($gradeLevel === 'kinder') {
            $assessments = KinderAssessment::where('school_year_id', $schoolYear->id)
                ->where('quarter', $quarter)
                ->where('status', 'submitted')
                ->get();

            foreach ($assessments as $assessment) {
                $oldStatus = $assessment->status;
                $assessment->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
                $this->auditLog->log('approved', $assessment, ['status' => $oldStatus], ['status' => 'approved']);
            }

            return redirect()->route('approvals.index')->with('success', 'Kindergarten assessments approved.');
        }

        $grades = Grade::where('school_year_id', $schoolYear->id)
            ->where('subject_id', $subjectId)
            ->where('quarter', $quarter)
            ->forGradeLevel($gradeLevel)
            ->where('status', 'submitted')
            ->get();

        foreach ($grades as $grade) {
            $oldStatus = $grade->status;
            $grade->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            $this->auditLog->log('approved', $grade, ['status' => $oldStatus], ['status' => 'approved']);
        }

        return redirect()->route('approvals.index')->with('success', 'Grades approved successfully.');
    }

    public function reject(Request $request, string $gradeLevel, ?string $subjectId, string $quarter)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $schoolYear = SchoolYear::current()->first();

        if ($gradeLevel === 'kinder') {
            $assessments = KinderAssessment::where('school_year_id', $schoolYear->id)
                ->where('quarter', $quarter)
                ->where('status', 'submitted')
                ->get();

            foreach ($assessments as $assessment) {
                $assessment->update(['status' => 'draft', 'submitted_at' => null]);
                $this->auditLog->log('rejected', $assessment, ['status' => 'submitted'], ['status' => 'draft', 'reason' => $request->reason]);
            }

            return redirect()->route('approvals.index')->with('success', 'Kindergarten assessments returned for revision.');
        }

        $grades = Grade::where('school_year_id', $schoolYear->id)
            ->where('subject_id', $subjectId)
            ->where('quarter', $quarter)
            ->forGradeLevel($gradeLevel)
            ->where('status', 'submitted')
            ->get();

        foreach ($grades as $grade) {
            $grade->update(['status' => 'draft', 'submitted_at' => null]);
            $this->auditLog->log('rejected', $grade, ['status' => 'submitted'], ['status' => 'draft', 'reason' => $request->reason]);
        }

        return redirect()->route('approvals.index')->with('success', 'Grades returned for revision.');
    }
}
