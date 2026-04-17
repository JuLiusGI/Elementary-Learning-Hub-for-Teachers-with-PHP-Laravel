<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Student::class, 'student');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Student::with(['schoolYear', 'teacher'])
            ->currentSchoolYear();

        if ($user->isTeacher()) {
            $query->forGradeLevel($user->grade_level);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%");
            });
        }

        if ($user->isHeadTeacher() && $gradeLevel = $request->get('grade_level')) {
            $query->forGradeLevel($gradeLevel);
        }

        if ($status = $request->get('status')) {
            $query->where('enrollment_status', $status);
        }

        $students = $query->orderBy('last_name')->orderBy('first_name')->paginate(20)->withQueryString();

        return view('students.index', compact('students'));
    }

    public function create()
    {
        $gradeLevels = config('school.grade_levels');
        $currentSchoolYear = SchoolYear::current()->first();

        return view('students.create', compact('gradeLevels', 'currentSchoolYear'));
    }

    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        $data['school_year_id'] = SchoolYear::current()->first()->id;

        if ($user->isTeacher()) {
            $data['teacher_id'] = $user->id;
            $data['grade_level'] = $user->grade_level;
        }

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('student-photos', 'public');
        }
        unset($data['photo']);

        Student::create($data);

        return redirect()->route('students.index')->with('success', 'Student added successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['schoolYear', 'teacher']);
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $gradeLevels = config('school.grade_levels');
        return view('students.edit', compact('student', 'gradeLevels'));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($student->photo_path) {
                Storage::disk('public')->delete($student->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('student-photos', 'public');
        }
        unset($data['photo']);

        $student->update($data);

        return redirect()->route('students.show', $student)->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        if ($student->photo_path) {
            Storage::disk('public')->delete($student->photo_path);
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}
