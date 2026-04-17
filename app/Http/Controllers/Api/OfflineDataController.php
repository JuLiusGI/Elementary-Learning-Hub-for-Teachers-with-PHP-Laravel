<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfflineDataController extends Controller
{
    /**
     * Get students for IndexedDB population (scoped by grade level).
     */
    public function students(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Student::currentSchoolYear()->active();

        if ($user->isTeacher()) {
            $query->forGradeLevel($user->grade_level);
        }

        $students = $query->orderBy('last_name')
            ->get(['id', 'lrn', 'first_name', 'middle_name', 'last_name',
                    'suffix', 'grade_level', 'enrollment_status']);

        return response()->json($students);
    }

    /**
     * Get subjects for IndexedDB population (scoped by grade level).
     */
    public function subjects(Request $request): JsonResponse
    {
        $user = $request->user();
        $gradeLevel = $user->isTeacher() ? $user->grade_level : $request->get('grade_level');

        $query = Subject::active()->orderBy('display_order');

        if ($gradeLevel) {
            $query->forGradeLevel($gradeLevel);
        }

        return response()->json($query->get(['id', 'name', 'code']));
    }

    /**
     * Get attendance records for a specific date.
     */
    public function attendance(Request $request): JsonResponse
    {
        $user = $request->user();
        $date = $request->get('date', now()->toDateString());

        $query = Attendance::with('student:id,first_name,last_name')
            ->forDate($date)
            ->currentSchoolYear();

        if ($user->isTeacher()) {
            $query->forGradeLevel($user->grade_level);
        }

        return response()->json($query->get());
    }

    /**
     * Get grades for a specific quarter.
     */
    public function grades(Request $request): JsonResponse
    {
        $user = $request->user();
        $quarter = $request->get('quarter', 'Q1');
        $schoolYear = SchoolYear::where('is_current', true)->first();

        if (!$schoolYear) {
            return response()->json([]);
        }

        $query = Grade::where('school_year_id', $schoolYear->id)
            ->where('quarter', $quarter);

        if ($user->isTeacher()) {
            $query->forGradeLevel($user->grade_level);
        }

        return response()->json($query->get());
    }
}
