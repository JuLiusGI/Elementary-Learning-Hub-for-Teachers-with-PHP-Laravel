<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKinderAssessmentRequest;
use App\Models\KinderAssessment;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Http\Request;

class KinderAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isTeacher() && $user->grade_level !== 'kinder') {
            abort(403);
        }

        $students = Student::currentSchoolYear()
            ->active()
            ->forGradeLevel('kinder')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $schoolYear = SchoolYear::current()->first();

        // Get assessment statuses per student per quarter
        $assessmentStatuses = [];
        if ($schoolYear) {
            $assessments = KinderAssessment::where('school_year_id', $schoolYear->id)->get();

            foreach ($students as $student) {
                foreach (config('school.quarters') as $quarter) {
                    $studentAssessments = $assessments->where('student_id', $student->id)->where('quarter', $quarter);
                    if ($studentAssessments->isEmpty()) {
                        $assessmentStatuses[$student->id][$quarter] = null;
                    } else {
                        $assessmentStatuses[$student->id][$quarter] = $studentAssessments->first()->status;
                    }
                }
            }
        }

        return view('kinder.index', compact('students', 'assessmentStatuses'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if ($user->isTeacher() && $user->grade_level !== 'kinder') {
            abort(403);
        }

        $quarter = $request->get('quarter', 'Q1');

        $students = Student::currentSchoolYear()
            ->active()
            ->forGradeLevel('kinder')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $schoolYear = SchoolYear::current()->first();
        $domains = config('school.kinder_domains');
        $ratings = config('school.kinder_ratings');

        // Load existing assessments
        $existingAssessments = [];
        if ($schoolYear) {
            $records = KinderAssessment::where('school_year_id', $schoolYear->id)
                ->where('quarter', $quarter)
                ->whereIn('student_id', $students->pluck('id'))
                ->get();

            foreach ($records as $record) {
                $existingAssessments[$record->student_id][$record->domain] = $record;
            }
        }

        $isLocked = collect($existingAssessments)->flatten()->contains(fn ($a) => $a->isLocked() || $a->isApproved());
        $isSubmitted = collect($existingAssessments)->flatten()->contains(fn ($a) => $a->isSubmitted());

        return view('kinder.create', compact('students', 'quarter', 'domains', 'ratings', 'existingAssessments', 'isLocked', 'isSubmitted'));
    }

    public function store(StoreKinderAssessmentRequest $request)
    {
        $data = $request->validated();
        $schoolYear = SchoolYear::current()->first();

        foreach ($data['assessments'] as $entry) {
            foreach ($entry['domains'] as $domainData) {
                if (empty($domainData['rating'])) {
                    continue;
                }

                KinderAssessment::updateOrCreate(
                    [
                        'student_id' => $entry['student_id'],
                        'school_year_id' => $schoolYear->id,
                        'quarter' => $data['quarter'],
                        'domain' => $domainData['domain'],
                    ],
                    [
                        'rating' => $domainData['rating'],
                        'remarks' => $domainData['remarks'] ?? null,
                        'status' => 'draft',
                    ]
                );
            }
        }

        return redirect()->route('kinder-assessments.index')
            ->with('success', 'Assessments saved as draft.');
    }

    public function show(Request $request, Student $student)
    {
        $user = $request->user();
        if ($user->isTeacher() && $user->grade_level !== 'kinder') {
            abort(403);
        }

        $schoolYear = SchoolYear::current()->first();
        $domains = config('school.kinder_domains');

        $assessments = KinderAssessment::where('student_id', $student->id)
            ->where('school_year_id', $schoolYear->id)
            ->get()
            ->groupBy('quarter');

        return view('kinder.show', compact('student', 'domains', 'assessments'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'quarter' => 'required|in:Q1,Q2,Q3,Q4',
        ]);

        $schoolYear = SchoolYear::current()->first();

        $assessments = KinderAssessment::where('school_year_id', $schoolYear->id)
            ->where('quarter', $request->quarter)
            ->forStatus('draft')
            ->get();

        if ($assessments->isEmpty()) {
            return back()->with('error', 'No draft assessments to submit.');
        }

        foreach ($assessments as $assessment) {
            $assessment->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
        }

        return redirect()->route('kinder-assessments.index')
            ->with('success', 'Assessments submitted for approval.');
    }
}
