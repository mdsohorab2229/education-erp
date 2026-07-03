<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\AttendanceRecordRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AttendanceBulkService
{
    public function __construct(
        private readonly AttendanceRecordRepositoryInterface $recordRepository,
        private readonly AttendanceSummaryService $summaryService,
    ) {}

    public function bulkUpdate(int $sessionId, array $studentIds, string $status): array
    {
        return DB::transaction(function () use ($sessionId, $studentIds, $status): array {
            $records = array_map(fn (int $studentId): array => [
                'attendance_session_id' => $sessionId,
                'student_id' => $studentId,
                'attendance_status' => $status,
                'checked_at' => now(),
            ], $studentIds);

            $this->recordRepository->bulkUpsert($records);

            return $this->summaryService->recalculate($sessionId);
        });
    }

    public function clearAttendance(int $sessionId, array $studentIds): array
    {
        return DB::transaction(function () use ($sessionId, $studentIds): array {
            $records = array_map(fn (int $studentId): array => [
                'attendance_session_id' => $sessionId,
                'student_id' => $studentId,
                'attendance_status' => null,
                'checked_at' => now(),
            ], $studentIds);

            $this->recordRepository->bulkUpsert($records);

            return $this->summaryService->recalculate($sessionId);
        });
    }
}
