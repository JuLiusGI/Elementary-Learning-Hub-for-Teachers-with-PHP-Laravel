<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $defaultGradeLevel = $user->isTeacher() ? $user->grade_level : 'grade_1';

        $studentsQuery = Student::currentSchoolYear()->active()
            ->orderBy('last_name')->orderBy('first_name');

        if ($user->isTeacher()) {
            $studentsQuery->forGradeLevel($user->grade_level);
        }

        $students = $studentsQuery->get()
            ->groupBy('grade_level')
            ->map(fn ($group) => $group->map(fn ($s) => ['id' => $s->id, 'name' => $s->full_name])->values());

        return view('reports.index', compact('defaultGradeLevel', 'students'));
    }

    public function sf9(Request $request, Student $student)
    {
        $user = $request->user();
        if ($user->isTeacher() && $user->grade_level !== $student->grade_level) {
            abort(403);
        }

        $schoolYear = SchoolYear::current()->firstOrFail();

        if ($student->grade_level === 'kinder') {
            return $this->sf9Kinder($student, $schoolYear);
        }

        $data = $this->reportService->getStudentSf9Data($student, $schoolYear);

        $pdf = Pdf::loadView('reports.pdf.sf9', $data)
            ->setPaper('letter', 'portrait');

        return $pdf->stream("SF9_{$student->lrn}_{$student->last_name}.pdf");
    }

    protected function sf9Kinder(Student $student, SchoolYear $schoolYear)
    {
        $data = $this->reportService->getStudentSf9KinderData($student, $schoolYear);

        $pdf = Pdf::loadView('reports.pdf.sf9-kinder', $data)
            ->setPaper('letter', 'portrait');

        return $pdf->stream("SF9_Kinder_{$student->lrn}_{$student->last_name}.pdf");
    }

    public function sf9Bulk(Request $request)
    {
        $user = $request->user();
        $gradeLevel = $user->isTeacher() ? $user->grade_level : $request->get('grade_level', 'grade_1');

        if ($user->isTeacher() && $user->grade_level !== $gradeLevel) {
            abort(403);
        }

        $schoolYear = SchoolYear::current()->firstOrFail();
        $students = Student::currentSchoolYear()->active()
            ->forGradeLevel($gradeLevel)
            ->orderBy('last_name')->orderBy('first_name')
            ->get();

        $isKinder = $gradeLevel === 'kinder';
        $studentsData = $students->map(fn ($student) => $isKinder
            ? $this->reportService->getStudentSf9KinderData($student, $schoolYear)
            : $this->reportService->getStudentSf9Data($student, $schoolYear)
        );

        $pdf = Pdf::loadView('reports.pdf.sf9-bulk', [
            'studentsData' => $studentsData,
            'isKinder' => $isKinder,
        ])->setPaper('letter', 'portrait');

        $levelLabel = config('school.grade_levels')[$gradeLevel] ?? $gradeLevel;

        return $pdf->stream("SF9_Bulk_{$levelLabel}.pdf");
    }

    public function sf10(Request $request, Student $student)
    {
        $user = $request->user();
        if ($user->isTeacher() && $user->grade_level !== $student->grade_level) {
            abort(403);
        }

        $data = $this->reportService->getStudentSf10Data($student);

        $pdf = Pdf::loadView('reports.pdf.sf10', $data)
            ->setPaper('letter', 'portrait');

        return $pdf->stream("SF10_{$student->lrn}_{$student->last_name}.pdf");
    }

    public function attendanceReport(Request $request)
    {
        $user = $request->user();
        $gradeLevel = $user->isTeacher() ? $user->grade_level : $request->get('grade_level', 'grade_1');
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        if ($user->isTeacher() && $user->grade_level !== $gradeLevel) {
            abort(403);
        }

        $data = $this->reportService->getMonthlyAttendanceReport($gradeLevel, $year, $month);
        $data['teacher'] = $user->isTeacher()
            ? $user
            : User::where('grade_level', $gradeLevel)->where('is_active', true)->first();

        $pdf = Pdf::loadView('reports.pdf.attendance-monthly', $data)
            ->setPaper('letter', 'portrait');

        return $pdf->stream("Attendance_{$gradeLevel}_{$year}_{$month}.pdf");
    }

    public function gradeSummary(Request $request)
    {
        $user = $request->user();
        $gradeLevel = $user->isTeacher() ? $user->grade_level : $request->get('grade_level', 'grade_1');
        $quarter = $request->get('quarter', 'Q1');

        if ($user->isTeacher() && $user->grade_level !== $gradeLevel) {
            abort(403);
        }

        $schoolYear = SchoolYear::current()->firstOrFail();
        $data = $this->reportService->getClassGradeSummary($gradeLevel, $schoolYear, $quarter);
        $data['teacher'] = $user->isTeacher()
            ? $user
            : User::where('grade_level', $gradeLevel)->where('is_active', true)->first();

        $pdf = Pdf::loadView('reports.pdf.grade-summary', $data)
            ->setPaper('letter', 'landscape');

        return $pdf->stream("GradeSummary_{$gradeLevel}_{$quarter}.pdf");
    }
}
