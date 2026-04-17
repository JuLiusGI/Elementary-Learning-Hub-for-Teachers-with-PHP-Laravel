<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $date = $request->get('date', now()->toDateString());

        $query = Attendance::with('student')
            ->forDate($date)
            ->currentSchoolYear();

        if ($user->isTeacher()) {
            $query->forGradeLevel($user->grade_level);
        } elseif ($gradeLevel = $request->get('grade_level')) {
            $query->forGradeLevel($gradeLevel);
        }

        $records = $query->get()->sortBy('student.last_name');

        // Summary counts
        $summary = [
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'late' => $records->where('status', 'late')->count(),
            'excused' => $records->where('status', 'excused')->count(),
            'total' => $records->count(),
        ];

        return view('attendance.index', compact('records', 'date', 'summary'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $date = $request->get('date', now()->toDateString());

        $studentQuery = Student::currentSchoolYear()->active();

        if ($user->isTeacher()) {
            $studentQuery->forGradeLevel($user->grade_level);
        } elseif ($gradeLevel = $request->get('grade_level')) {
            $studentQuery->forGradeLevel($gradeLevel);
        }

        $students = $studentQuery->orderBy('last_name')->orderBy('first_name')->get();

        // Load existing records for this date
        $existingRecords = Attendance::forDate($date)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        return view('attendance.create', compact('students', 'date', 'existingRecords'));
    }

    public function store(StoreAttendanceRequest $request)
    {
        $data = $request->validated();
        $userId = $request->user()->id;

        foreach ($data['attendance'] as $entry) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $entry['student_id'],
                    'date' => $data['date'],
                ],
                [
                    'status' => $entry['status'],
                    'time_in' => $entry['status'] === 'late' ? ($entry['time_in'] ?? null) : null,
                    'remarks' => $entry['remarks'] ?? null,
                    'recorded_by' => $userId,
                ]
            );
        }

        return redirect()->route('attendance.index', ['date' => $data['date']])
            ->with('success', 'Attendance recorded successfully.');
    }

    public function show(Request $request, Student $student)
    {
        $user = $request->user();

        if ($user->isTeacher() && $user->grade_level !== $student->grade_level) {
            abort(403);
        }

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $records = Attendance::where('student_id', $student->id)
            ->forMonth($year, $month)
            ->orderBy('date')
            ->get()
            ->keyBy(fn ($r) => $r->date->format('Y-m-d'));

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $monthName = Carbon::create($year, $month)->format('F Y');

        return view('attendance.show', compact('student', 'records', 'year', 'month', 'daysInMonth', 'monthName'));
    }

    public function summary(Request $request)
    {
        $user = $request->user();
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $studentQuery = Student::currentSchoolYear()->active();

        if ($user->isTeacher()) {
            $studentQuery->forGradeLevel($user->grade_level);
        } elseif ($gradeLevel = $request->get('grade_level')) {
            $studentQuery->forGradeLevel($gradeLevel);
        }

        $students = $studentQuery->orderBy('last_name')->orderBy('first_name')->get();

        // Get attendance records for the month
        $records = Attendance::whereIn('student_id', $students->pluck('id'))
            ->forMonth($year, $month)
            ->get();

        // Calculate summary per student
        $summaryData = $students->map(function ($student) use ($records) {
            $studentRecords = $records->where('student_id', $student->id);
            return [
                'student' => $student,
                'present' => $studentRecords->where('status', 'present')->count(),
                'absent' => $studentRecords->where('status', 'absent')->count(),
                'late' => $studentRecords->where('status', 'late')->count(),
                'excused' => $studentRecords->where('status', 'excused')->count(),
                'total_days' => $studentRecords->count(),
            ];
        });

        $monthName = Carbon::create($year, $month)->format('F Y');

        return view('attendance.summary', compact('summaryData', 'year', 'month', 'monthName'));
    }
}
