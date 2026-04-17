<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherRequest;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLog
    ) {}

    public function index(Request $request)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);

        $teachers = User::where('role', 'teacher')
            ->withCount(['students' => fn ($q) => $q->active()])
            ->orderBy('name')
            ->get();

        return view('teachers.index', compact('teachers'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);

        return view('teachers.create');
    }

    public function store(TeacherRequest $request)
    {
        $data = $request->validated();

        $teacher = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'teacher',
            'grade_level' => $data['grade_level'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        $this->auditLog->log('teacher.created', $teacher, null, [
            'name' => $teacher->name,
            'email' => $teacher->email,
            'grade_level' => $teacher->grade_level,
        ]);

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher account created successfully.');
    }

    public function show(Request $request, User $teacher)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);
        abort_unless($teacher->role === 'teacher', 404);

        $teacher->loadCount(['students' => fn ($q) => $q->active()]);

        $students = $teacher->students()
            ->active()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('teachers.show', compact('teacher', 'students'));
    }

    public function edit(Request $request, User $teacher)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);
        abort_unless($teacher->role === 'teacher', 404);

        return view('teachers.edit', compact('teacher'));
    }

    public function update(TeacherRequest $request, User $teacher)
    {
        abort_unless($teacher->role === 'teacher', 404);

        $oldValues = $teacher->only(['name', 'email', 'grade_level', 'is_active']);
        $data = $request->validated();

        $teacher->name = $data['name'];
        $teacher->email = $data['email'];
        $teacher->grade_level = $data['grade_level'];
        $teacher->is_active = $data['is_active'] ?? true;

        if (!empty($data['password'])) {
            $teacher->password = Hash::make($data['password']);
        }

        $teacher->save();

        $this->auditLog->log('teacher.updated', $teacher, $oldValues, $teacher->only(['name', 'email', 'grade_level', 'is_active']));

        return redirect()->route('teachers.show', $teacher)
            ->with('success', 'Teacher account updated successfully.');
    }

    public function destroy(Request $request, User $teacher)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);
        abort_unless($teacher->role === 'teacher', 404);

        $activeStudents = $teacher->students()->active()->count();
        if ($activeStudents > 0) {
            return back()->with('error', "Cannot deactivate teacher with {$activeStudents} active student(s). Reassign students first.");
        }

        $oldValues = ['is_active' => $teacher->is_active];
        $teacher->update(['is_active' => false]);

        $this->auditLog->log('teacher.deactivated', $teacher, $oldValues, ['is_active' => false]);

        return redirect()->route('teachers.index')
            ->with('success', 'Teacher account deactivated successfully.');
    }
}
