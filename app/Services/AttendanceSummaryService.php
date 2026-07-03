<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Interfaces\Repositories\AttendanceSessionRepositoryInterface;
use App\Interfaces\Repositories\AttendanceRecordRepositoryInterface;

class AttendanceSummaryService
{
    public function __construct(
        private readonly AttendanceSessionRepositoryInterface $sessionRepository,
        private readonly AttendanceRecordRepositoryInterface $recordRepository,
    ) {}

    public function recalculate(int $sessionId): array
    {
        $session = $this->sessionRepository->findById($sessionId);

        if (!$session) {
            throw new \RuntimeException("Attendance session with ID {$sessionId} not found.");
        }

        $records = $this->recordRepository->getBySession($sessionId);

        $present = $records->where('attendance_status', AttendanceStatus::PRESENT->value)->count();
        $absent = $records->where('attendance_status', AttendanceStatus::ABSENT->value)->count();
        $late = $records->where('attendance_status', AttendanceStatus::LATE->value)->count();
        $leave = $records->where('attendance_status', AttendanceStatus::LEAVE->value)->count();

        $this->sessionRepository->updateSummary($sessionId, [
            'present_count' => $present,
            'absent_count' => $absent,
            'late_count' => $late,
            'leave_count' => $leave,
        ]);

        return [
            'total' => $present + $absent + $late + $leave,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'leave' => $leave,
        ];
    }

    public function getSummary(int $sessionId): array
    {
        $session = $this->sessionRepository->findById($sessionId);

        if (!$session) {
            throw new \RuntimeException("Attendance session with ID {$sessionId} not found.");
        }

        return [
            'total' => $session->total_students,
            'present' => $session->present_count,
            'absent' => $session->absent_count,
            'late' => $session->late_count,
            'leave' => $session->leave_count,
        ];
    }
}
