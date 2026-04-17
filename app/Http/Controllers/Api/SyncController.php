<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OfflineSyncQueue;
use App\Services\SyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function __construct(
        private SyncService $syncService,
    ) {}

    /**
     * Sync offline attendance records to server.
     */
    public function syncAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|max:200',
            'items.*.client_id' => 'required|string|max:36',
            'items.*.payload' => 'required|array',
            'items.*.payload.student_id' => 'required|exists:students,id',
            'items.*.payload.date' => 'required|date',
            'items.*.payload.status' => 'required|in:present,absent,late,excused',
            'items.*.client_timestamp' => 'required|string',
        ]);

        $results = $this->syncService->processAttendanceItems(
            $request->input('items'),
            $request->user()->id,
        );

        return response()->json(['results' => $results]);
    }

    /**
     * Sync offline grade records to server.
     */
    public function syncGrades(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|max:200',
            'items.*.client_id' => 'required|string|max:36',
            'items.*.payload' => 'required|array',
            'items.*.payload.student_id' => 'required|exists:students,id',
            'items.*.payload.subject_id' => 'required|exists:subjects,id',
            'items.*.payload.quarter' => 'required|in:Q1,Q2,Q3,Q4',
            'items.*.client_timestamp' => 'required|string',
        ]);

        $results = $this->syncService->processGradeItems(
            $request->input('items'),
            $request->user()->id,
        );

        return response()->json(['results' => $results]);
    }

    /**
     * Get sync status counts for the current user.
     */
    public function status(Request $request): JsonResponse
    {
        $counts = OfflineSyncQueue::where('user_id', $request->user()->id)
            ->selectRaw('sync_status, COUNT(*) as count')
            ->groupBy('sync_status')
            ->pluck('count', 'sync_status');

        return response()->json([
            'pending' => $counts['pending'] ?? 0,
            'synced' => $counts['synced'] ?? 0,
            'conflict' => $counts['conflict'] ?? 0,
            'failed' => $counts['failed'] ?? 0,
        ]);
    }
}
