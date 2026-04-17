<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Api\OfflineDataController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LearningMaterialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeApprovalController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\KinderAssessmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('students', StudentController::class);

    Route::resource('attendance', AttendanceController::class)->only(['index', 'create', 'store']);
    Route::get('attendance/summary', [AttendanceController::class, 'summary'])->name('attendance.summary');
    Route::get('attendance/student/{student}', [AttendanceController::class, 'show'])->name('attendance.show');

    Route::resource('grades', GradeController::class)->only(['index', 'create', 'store']);
    Route::get('grades/student/{student}', [GradeController::class, 'show'])->name('grades.show');
    Route::post('grades/submit', [GradeController::class, 'submit'])->name('grades.submit');

    Route::resource('kinder-assessments', KinderAssessmentController::class)->only(['index', 'create', 'store']);
    Route::get('kinder-assessments/student/{student}', [KinderAssessmentController::class, 'show'])->name('kinder-assessments.show');
    Route::post('kinder-assessments/submit', [KinderAssessmentController::class, 'submit'])->name('kinder-assessments.submit');

    Route::resource('assignments', AssignmentController::class);
    Route::get('assignments/{assignment}/scores', [AssignmentController::class, 'scores'])->name('assignments.scores');
    Route::post('assignments/{assignment}/scores', [AssignmentController::class, 'storeScores'])->name('assignments.scores.store');

    Route::resource('learning-materials', LearningMaterialController::class);
    Route::get('learning-materials/{learning_material}/download', [LearningMaterialController::class, 'download'])->name('learning-materials.download');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sf9/{student}', [ReportController::class, 'sf9'])->name('reports.sf9');
    Route::get('reports/sf9-bulk', [ReportController::class, 'sf9Bulk'])->name('reports.sf9-bulk');
    Route::get('reports/sf10/{student}', [ReportController::class, 'sf10'])->name('reports.sf10');
    Route::get('reports/attendance', [ReportController::class, 'attendanceReport'])->name('reports.attendance');
    Route::get('reports/grade-summary', [ReportController::class, 'gradeSummary'])->name('reports.grade-summary');

    Route::resource('announcements', AnnouncementController::class);

    Route::resource('teachers', TeacherController::class);

    Route::resource('school-years', SchoolYearController::class)->except(['show', 'destroy']);
    Route::post('school-years/{school_year}/activate', [SchoolYearController::class, 'activate'])->name('school-years.activate');
    Route::post('school-years/{school_year}/archive', [SchoolYearController::class, 'archive'])->name('school-years.archive');

    Route::get('promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('promotions/review', [PromotionController::class, 'review'])->name('promotions.review');
    Route::post('promotions/process', [PromotionController::class, 'process'])->name('promotions.process');
    Route::get('promotions/history', [PromotionController::class, 'history'])->name('promotions.history');

    Route::get('approvals', [GradeApprovalController::class, 'index'])->name('approvals.index');
    Route::get('approvals/{gradeLevel}/{subjectId}/{quarter}', [GradeApprovalController::class, 'show'])->name('approvals.show');
    Route::post('approvals/{gradeLevel}/{subjectId}/{quarter}/approve', [GradeApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('approvals/{gradeLevel}/{subjectId}/{quarter}/reject', [GradeApprovalController::class, 'reject'])->name('approvals.reject');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Offline/Sync API routes (session-authenticated, JSON responses)
    Route::prefix('api')->group(function () {
        Route::get('offline/students', [OfflineDataController::class, 'students']);
        Route::get('offline/subjects', [OfflineDataController::class, 'subjects']);
        Route::get('offline/attendance', [OfflineDataController::class, 'attendance']);
        Route::get('offline/grades', [OfflineDataController::class, 'grades']);

        Route::post('sync/attendance', [SyncController::class, 'syncAttendance']);
        Route::post('sync/grades', [SyncController::class, 'syncGrades']);
        Route::get('sync/status', [SyncController::class, 'status']);
    });
});

require __DIR__.'/auth.php';
