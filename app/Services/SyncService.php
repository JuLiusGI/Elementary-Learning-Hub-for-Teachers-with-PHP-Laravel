<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Grade;
use App\Models\OfflineSyncQueue;
use App\Models\SchoolYear;
use Carbon\Carbon;

class SyncService
{
    public function __construct(
        private GradeCalculatorService $calculator,
    ) {}

    /**
     * Process queued attendance items from offline sync.
     * Uses server-wins conflict resolution.
     */
    public function processAttendanceItems(array $items, int $userId): array
    {
        $results = [];

        foreach ($items as $item) {
            $clientId = $item['client_id'];
            $payload = $item['payload'];
            $clientTimestamp = Carbon::parse($item['client_timestamp']);

            try {
                $existing = Attendance::where('student_id', $payload['student_id'])
                    ->where('date', $payload['date'])
                    ->first();

                // Server-wins: if server record is newer, skip client version
                if ($existing && $existing->updated_at->gt($clientTimestamp)) {
                    $this->logSync($userId, $clientId, 'attendance', $existing->id, $payload, 'conflict');
                    $results[] = ['client_id' => $clientId, 'status' => 'conflict', 'server_data' => $existing];
                    continue;
                }

                $record = Attendance::updateOrCreate(
                    [
                        'student_id' => $payload['student_id'],
                        'date' => $payload['date'],
                    ],
                    [
                        'status' => $payload['status'],
                        'time_in' => $payload['status'] === 'late' ? ($payload['time_in'] ?? null) : null,
                        'remarks' => $payload['remarks'] ?? null,
                        'recorded_by' => $userId,
                        'client_id' => $clientId,
                        'synced_at' => now(),
                    ]
                );

                $this->logSync($userId, $clientId, 'attendance', $record->id, $payload, 'synced');
                $results[] = ['client_id' => $clientId, 'status' => 'synced'];
            } catch (\Exception $e) {
                $this->logSync($userId, $clientId, 'attendance', null, $payload, 'failed', $e->getMessage());
                $results[] = ['client_id' => $clientId, 'status' => 'failed', 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Process queued grade items from offline sync.
     * Won't overwrite approved/locked grades.
     */
    public function processGradeItems(array $items, int $userId): array
    {
        $results = [];
        $schoolYear = SchoolYear::where('is_current', true)->first();

        if (!$schoolYear) {
            return [['status' => 'failed', 'error' => 'No active school year']];
        }

        foreach ($items as $item) {
            $clientId = $item['client_id'];
            $payload = $item['payload'];
            $clientTimestamp = Carbon::parse($item['client_timestamp']);

            try {
                $existing = Grade::where('student_id', $payload['student_id'])
                    ->where('subject_id', $payload['subject_id'])
                    ->where('school_year_id', $schoolYear->id)
                    ->where('quarter', $payload['quarter'])
                    ->first();

                // Don't overwrite approved/locked grades
                if ($existing && in_array($existing->status, ['approved', 'locked'])) {
                    $this->logSync($userId, $clientId, 'grades', $existing->id, $payload, 'conflict');
                    $results[] = ['client_id' => $clientId, 'status' => 'conflict', 'reason' => 'grade_locked'];
                    continue;
                }

                // Server-wins if server is newer
                if ($existing && $existing->updated_at->gt($clientTimestamp)) {
                    $this->logSync($userId, $clientId, 'grades', $existing->id, $payload, 'conflict');
                    $results[] = ['client_id' => $clientId, 'status' => 'conflict'];
                    continue;
                }

                // Calculate weighted grades using existing service
                $calculated = $this->calculator->calculate(
                    $payload['ww_total_score'] ?? null,
                    $payload['ww_max_score'] ?? null,
                    $payload['pt_total_score'] ?? null,
                    $payload['pt_max_score'] ?? null,
                    $payload['qa_score'] ?? null,
                    $payload['qa_max_score'] ?? null,
                );

                $record = Grade::updateOrCreate(
                    [
                        'student_id' => $payload['student_id'],
                        'subject_id' => $payload['subject_id'],
                        'school_year_id' => $schoolYear->id,
                        'quarter' => $payload['quarter'],
                    ],
                    array_merge([
                        'ww_total_score' => $payload['ww_total_score'] ?? null,
                        'ww_max_score' => $payload['ww_max_score'] ?? null,
                        'pt_total_score' => $payload['pt_total_score'] ?? null,
                        'pt_max_score' => $payload['pt_max_score'] ?? null,
                        'qa_score' => $payload['qa_score'] ?? null,
                        'qa_max_score' => $payload['qa_max_score'] ?? null,
                        'status' => 'draft',
                        'client_id' => $clientId,
                        'synced_at' => now(),
                    ], $calculated)
                );

                $this->logSync($userId, $clientId, 'grades', $record->id, $payload, 'synced');
                $results[] = ['client_id' => $clientId, 'status' => 'synced'];
            } catch (\Exception $e) {
                $this->logSync($userId, $clientId, 'grades', null, $payload, 'failed', $e->getMessage());
                $results[] = ['client_id' => $clientId, 'status' => 'failed', 'error' => $e->getMessage()];
            }
        }

        return $results;
    }

    private function logSync(
        int $userId,
        string $clientId,
        string $modelType,
        ?int $modelId,
        array $payload,
        string $status,
        ?string $error = null,
    ): void {
        OfflineSyncQueue::updateOrCreate(
            ['client_id' => $clientId],
            [
                'user_id' => $userId,
                'action' => 'create',
                'model_type' => $modelType,
                'model_id' => $modelId,
                'payload' => $payload,
                'client_timestamp' => now(),
                'synced_at' => $status === 'synced' ? now() : null,
                'sync_status' => $status,
                'error_message' => $error,
            ]
        );
    }
}
