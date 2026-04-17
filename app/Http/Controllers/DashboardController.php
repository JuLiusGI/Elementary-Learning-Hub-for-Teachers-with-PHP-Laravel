<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\KinderAssessment;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isHeadTeacher()) {
            return $this->headTeacherDashboard();
        }

        return $this->teacherDashboard($user);
    }

    private function teacherDashboard(User $user)
    {
        $gradeLevel = $user->grade_level;
        $studentCount = Student::currentSchoolYear()->active()->forGradeLevel($gradeLevel)->count();

        // Basic stats
        $attendanceTodayCount = Attendance::forDate(today())->forGradeLevel($gradeLevel)->count();
        $draftGradesCount = $gradeLevel === 'kinder'
            ? KinderAssessment::forStatus('draft')->currentSchoolYear()->count()
            : Grade::forGradeLevel($gradeLevel)->forStatus('draft')->currentSchoolYear()->count();
        $assignmentCount = Assignment::forGradeLevel($gradeLevel)->currentSchoolYear()->count();

        // Attendance breakdown for today
        $attendanceBreakdown = Attendance::forDate(today())
            ->forGradeLevel($gradeLevel)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $attendanceBreakdown = [
            'present' => $attendanceBreakdown['present'] ?? 0,
            'absent' => $attendanceBreakdown['absent'] ?? 0,
            'late' => $attendanceBreakdown['late'] ?? 0,
            'excused' => $attendanceBreakdown['excused'] ?? 0,
        ];
        $attendanceRecorded = array_sum($attendanceBreakdown) > 0;

        // Pending grade tasks (subjects with missing grades)
        $pendingGradeTasks = $this->getPendingGradeTasks($user);

        // Students at risk (3+ absences this month)
        $atRiskStudents = $this->getAtRiskStudents($gradeLevel);

        $announcements = Announcement::published()->byPriority()->orderByDesc('created_at')->limit(3)->get();

        return view('dashboard.teacher', [
            'gradeLevel' => config('school.grade_levels')[$gradeLevel] ?? $gradeLevel,
            'studentCount' => $studentCount,
            'attendanceTodayCount' => $attendanceTodayCount,
            'totalStudents' => $studentCount,
            'draftGradesCount' => $draftGradesCount,
            'assignmentCount' => $assignmentCount,
            'announcements' => $announcements,
            'attendanceBreakdown' => $attendanceBreakdown,
            'attendanceRecorded' => $attendanceRecorded,
            'pendingGradeTasks' => $pendingGradeTasks,
            'atRiskStudents' => $atRiskStudents,
        ]);
    }

    private function headTeacherDashboard()
    {
        $pendingGrades = Grade::forStatus('submitted')->currentSchoolYear()->count();
        $pendingKinder = KinderAssessment::where('status', 'submitted')->currentSchoolYear()->count();
        $currentSchoolYear = SchoolYear::current()->first();
        $totalStudents = Student::currentSchoolYear()->active()->count();

        // School-wide attendance overview for today
        $attendanceTodayCounts = Attendance::forDate(today())
            ->currentSchoolYear()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $attendanceOverview = [
            'total_students' => $totalStudents,
            'present' => $attendanceTodayCounts['present'] ?? 0,
            'absent' => $attendanceTodayCounts['absent'] ?? 0,
            'late' => $attendanceTodayCounts['late'] ?? 0,
            'excused' => $attendanceTodayCounts['excused'] ?? 0,
        ];
        $attendanceOverview['recorded'] = array_sum($attendanceTodayCounts);
        $attendanceOverview['not_recorded'] = max(0, $totalStudents - $attendanceOverview['recorded']);

        // Grade submission status per teacher (enhanced grade level overview)
        $gradeSubmissionStatus = collect(config('school.grade_levels'))->map(function ($label, $key) {
            $teacher = User::where('role', 'teacher')->where('grade_level', $key)->first();
            $studentCount = Student::currentSchoolYear()->active()->forGradeLevel($key)->count();

            if ($key === 'kinder') {
                $statusCounts = KinderAssessment::currentSchoolYear()
                    ->select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray();
            } else {
                $statusCounts = Grade::forGradeLevel($key)->currentSchoolYear()
                    ->select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray();
            }

            return [
                'grade_level' => $key,
                'label' => $label,
                'teacher' => $teacher?->name ?? 'Unassigned',
                'students' => $studentCount,
                'draft' => $statusCounts['draft'] ?? 0,
                'submitted' => $statusCounts['submitted'] ?? 0,
                'approved' => $statusCounts['approved'] ?? 0,
                'locked' => $statusCounts['locked'] ?? 0,
            ];
        })->values();

        // Pending approval details
        $pendingApprovals = Grade::forStatus('submitted')
            ->currentSchoolYear()
            ->with(['student', 'subject'])
            ->get()
            ->groupBy(fn ($g) => $g->student->grade_level . '|' . $g->subject_id . '|' . $g->quarter)
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'grade_level' => $first->student->grade_level,
                    'grade_level_label' => config('school.grade_levels')[$first->student->grade_level] ?? $first->student->grade_level,
                    'subject' => $first->subject->name,
                    'subject_id' => $first->subject_id,
                    'quarter' => $first->quarter,
                    'count' => $group->count(),
                ];
            })->values();

        // Also get pending kinder assessments
        $pendingKinderApprovals = KinderAssessment::where('status', 'submitted')
            ->currentSchoolYear()
            ->with('student')
            ->get()
            ->groupBy(fn ($a) => $a->quarter . '|' . $a->domain)
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'grade_level' => 'kinder',
                    'grade_level_label' => 'Kindergarten',
                    'subject' => $first->domain_label,
                    'subject_id' => $first->domain,
                    'quarter' => $first->quarter,
                    'count' => $group->count(),
                ];
            })->values();

        $allPendingApprovals = $pendingApprovals->merge($pendingKinderApprovals);

        // Active announcements count
        $activeAnnouncementsCount = Announcement::published()->count();
        $announcements = Announcement::published()->byPriority()->orderByDesc('created_at')->limit(3)->get();

        return view('dashboard.head-teacher', [
            'teacherCount' => User::where('role', 'teacher')->active()->count(),
            'studentCount' => $totalStudents,
            'pendingApprovalsCount' => $pendingGrades + $pendingKinder,
            'gradeSubmissionStatus' => $gradeSubmissionStatus,
            'currentSchoolYear' => $currentSchoolYear,
            'announcements' => $announcements,
            'attendanceOverview' => $attendanceOverview,
            'allPendingApprovals' => $allPendingApprovals,
            'activeAnnouncementsCount' => $activeAnnouncementsCount,
        ]);
    }

    private function getPendingGradeTasks(User $user): array
    {
        $gradeLevel = $user->grade_level;
        $tasks = [];

        if ($gradeLevel === 'kinder') {
            $students = Student::currentSchoolYear()->active()->forGradeLevel('kinder')->count();
            if ($students === 0) {
                return [];
            }

            $domains = config('school.kinder_domains');
            $quarters = config('school.quarters');

            foreach ($quarters as $quarter) {
                foreach ($domains as $domainKey => $domainLabel) {
                    $entered = KinderAssessment::currentSchoolYear()
                        ->forQuarter($quarter)
                        ->where('domain', $domainKey)
                        ->count();
                    $missing = $students - $entered;
                    if ($missing > 0) {
                        $tasks[] = [
                            'label' => "{$domainLabel} {$quarter}",
                            'missing' => $missing,
                            'route' => route('kinder-assessments.create', ['quarter' => $quarter]),
                        ];
                    }
                }
            }
        } else {
            $students = Student::currentSchoolYear()->active()->forGradeLevel($gradeLevel)->count();
            if ($students === 0) {
                return [];
            }

            $subjects = Subject::forGradeLevel($gradeLevel)->active()->get();
            $quarters = config('school.quarters');

            foreach ($quarters as $quarter) {
                foreach ($subjects as $subject) {
                    $entered = Grade::forGradeLevel($gradeLevel)
                        ->currentSchoolYear()
                        ->forQuarter($quarter)
                        ->where('subject_id', $subject->id)
                        ->count();
                    $missing = $students - $entered;
                    if ($missing > 0) {
                        $tasks[] = [
                            'label' => "{$subject->name} {$quarter}",
                            'missing' => $missing,
                            'route' => route('grades.create', ['subject_id' => $subject->id, 'quarter' => $quarter]),
                        ];
                    }
                }
            }
        }

        return array_slice($tasks, 0, 5);
    }

    private function getAtRiskStudents(string $gradeLevel): \Illuminate\Support\Collection
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return Student::currentSchoolYear()
            ->active()
            ->forGradeLevel($gradeLevel)
            ->withCount(['attendances as absence_count' => function ($query) use ($currentMonth, $currentYear) {
                $query->where('status', 'absent')
                    ->whereYear('date', $currentYear)
                    ->whereMonth('date', $currentMonth);
            }])
            ->get()
            ->filter(fn ($student) => $student->absence_count >= 3)
            ->sortByDesc('absence_count')
            ->take(10)
            ->values();
    }
}
