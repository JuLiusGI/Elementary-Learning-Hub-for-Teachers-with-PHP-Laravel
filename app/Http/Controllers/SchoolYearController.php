<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLog
    ) {}

    public function index(Request $request)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);

        $schoolYears = SchoolYear::withCount('students')
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        return view('school-years.index', compact('schoolYears'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);

        return view('school-years.create');
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:school_years,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        $schoolYear = SchoolYear::create($data);

        $this->auditLog->log('school_year.created', $schoolYear, null, $data);

        return redirect()->route('school-years.index')
            ->with('success', 'School year created successfully.');
    }

    public function edit(Request $request, SchoolYear $schoolYear)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);
        abort_if($schoolYear->is_archived, 403, 'Cannot edit an archived school year.');

        return view('school-years.edit', compact('schoolYear'));
    }

    public function update(Request $request, SchoolYear $schoolYear)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);
        abort_if($schoolYear->is_archived, 403, 'Cannot edit an archived school year.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:school_years,name,' . $schoolYear->id],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);

        $oldValues = $schoolYear->only(['name', 'start_date', 'end_date']);
        $schoolYear->update($data);

        $this->auditLog->log('school_year.updated', $schoolYear, $oldValues, $data);

        return redirect()->route('school-years.index')
            ->with('success', 'School year updated successfully.');
    }

    public function activate(Request $request, SchoolYear $schoolYear)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);
        abort_if($schoolYear->is_archived, 403, 'Cannot activate an archived school year.');

        // Deactivate all other school years
        SchoolYear::where('is_current', true)->update(['is_current' => false]);

        $schoolYear->update(['is_current' => true]);

        $this->auditLog->log('school_year.activated', $schoolYear, null, ['is_current' => true]);

        return redirect()->route('school-years.index')
            ->with('success', "School year \"{$schoolYear->name}\" is now the current school year.");
    }

    public function archive(Request $request, SchoolYear $schoolYear)
    {
        abort_unless($request->user()->isHeadTeacher(), 403);

        if ($schoolYear->is_current) {
            return back()->with('error', 'Cannot archive the current school year. Activate another school year first.');
        }

        $schoolYear->update([
            'is_archived' => true,
            'archived_at' => now(),
        ]);

        $this->auditLog->log('school_year.archived', $schoolYear, null, ['is_archived' => true]);

        return redirect()->route('school-years.index')
            ->with('success', "School year \"{$schoolYear->name}\" has been archived.");
    }
}
