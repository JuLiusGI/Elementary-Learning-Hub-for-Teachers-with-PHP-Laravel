<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLearningMaterialRequest;
use App\Http\Requests\UpdateLearningMaterialRequest;
use App\Models\LearningMaterial;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LearningMaterialController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(LearningMaterial::class, 'learning_material');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $gradeLevel = $user->isTeacher() ? $user->grade_level : $request->get('grade_level');

        $query = LearningMaterial::with(['subject', 'uploader'])
            ->orderByDesc('created_at');

        if ($gradeLevel) {
            $query->forGradeLevel($gradeLevel);
        }

        if ($request->filled('subject_id')) {
            $query->forSubject($request->subject_id);
        }
        if ($request->filled('quarter')) {
            $query->forQuarter($request->quarter);
        }
        if ($request->filled('week_number')) {
            $query->forWeek($request->week_number);
        }
        if ($request->filled('file_type')) {
            $query->forFileType($request->file_type);
        }

        $materials = $query->paginate(20)->withQueryString();

        $subjects = Subject::active()
            ->when($gradeLevel, fn ($q) => $q->forGradeLevel($gradeLevel))
            ->orderBy('display_order')
            ->get();

        return view('materials.index', compact('materials', 'subjects', 'gradeLevel'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $gradeLevel = $user->isTeacher() ? $user->grade_level : $request->get('grade_level', 'grade_1');

        $subjects = Subject::active()
            ->forGradeLevel($gradeLevel)
            ->orderBy('display_order')
            ->get();

        return view('materials.create', compact('subjects', 'gradeLevel'));
    }

    public function store(StoreLearningMaterialRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $data['uploaded_by'] = $user->id;
        if ($user->isTeacher()) {
            $data['grade_level'] = $user->grade_level;
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store("materials/{$data['grade_level']}/{$data['subject_id']}", 'public');
            $data['file_path'] = $path;
            $data['file_size'] = $file->getSize();
            $data['mime_type'] = $file->getMimeType();
        }

        $data['is_downloadable'] = $request->boolean('is_downloadable', true);
        unset($data['file']);

        LearningMaterial::create($data);

        return redirect()->route('learning-materials.index')
            ->with('success', 'Material uploaded successfully.');
    }

    public function show(LearningMaterial $learningMaterial)
    {
        $learningMaterial->load(['subject', 'uploader']);

        return view('materials.show', compact('learningMaterial'));
    }

    public function edit(LearningMaterial $learningMaterial)
    {
        $subjects = Subject::active()
            ->forGradeLevel($learningMaterial->grade_level)
            ->orderBy('display_order')
            ->get();

        return view('materials.edit', compact('learningMaterial', 'subjects'));
    }

    public function update(UpdateLearningMaterialRequest $request, LearningMaterial $learningMaterial)
    {
        $data = $request->validated();

        // Handle file replacement
        if ($request->hasFile('file')) {
            // Delete old file
            if ($learningMaterial->file_path) {
                Storage::disk('public')->delete($learningMaterial->file_path);
            }

            $file = $request->file('file');
            $gradeLevel = $data['grade_level'] ?? $learningMaterial->grade_level;
            $subjectId = $data['subject_id'] ?? $learningMaterial->subject_id;
            $path = $file->store("materials/{$gradeLevel}/{$subjectId}", 'public');
            $data['file_path'] = $path;
            $data['file_size'] = $file->getSize();
            $data['mime_type'] = $file->getMimeType();
        }

        if ($request->has('is_downloadable')) {
            $data['is_downloadable'] = $request->boolean('is_downloadable');
        }
        unset($data['file']);

        $learningMaterial->update($data);

        return redirect()->route('learning-materials.show', $learningMaterial)
            ->with('success', 'Material updated successfully.');
    }

    public function destroy(LearningMaterial $learningMaterial)
    {
        // Delete file from storage
        if ($learningMaterial->file_path) {
            Storage::disk('public')->delete($learningMaterial->file_path);
        }

        $learningMaterial->delete();

        return redirect()->route('learning-materials.index')
            ->with('success', 'Material deleted successfully.');
    }

    public function download(LearningMaterial $learningMaterial)
    {
        $this->authorize('view', $learningMaterial);

        if (!$learningMaterial->file_path || !Storage::disk('public')->exists($learningMaterial->file_path)) {
            abort(404, 'File not found.');
        }

        $learningMaterial->incrementDownloadCount();

        $extension = pathinfo($learningMaterial->file_path, PATHINFO_EXTENSION);
        $filename = $learningMaterial->title . '.' . $extension;

        return Storage::disk('public')->download($learningMaterial->file_path, $filename);
    }
}
