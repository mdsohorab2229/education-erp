<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\AttendanceRecordRepositoryInterface;
use App\Models\AttendanceRecord;
use Illuminate\Database\Eloquent\Collection;

class AttendanceRecordRepository implements AttendanceRecordRepositoryInterface
{
    public function __construct(
        private readonly AttendanceRecord $model
    ) {}

    public function getBySession(int $sessionId): Collection
    {
        return $this->model
            ->where('attendance_session_id', $sessionId)
            ->get();
    }

    public function getBySessionWithStudent(int $sessionId): Collection
    {
        return $this->model
            ->with([
                'student.section',
                'student.program',
            ])
            ->where('attendance_session_id', $sessionId)
            ->get();
    }

    public function getStudentAttendance(int $sessionId, int $studentId): ?AttendanceRecord
    {
        return $this->model
            ->where('attendance_session_id', $sessionId)
            ->where('student_id', $studentId)
            ->first();
    }

    public function upsert(array $data): AttendanceRecord
    {
        $record = $this->model->updateOrCreate(
            [
                'attendance_session_id' => $data['attendance_session_id'],
                'student_id' => $data['student_id'],
            ],
            [
                'attendance_status' => $data['attendance_status'] ?? null,
                'remark' => $data['remark'] ?? null,
                'checked_at' => $data['checked_at'] ?? now(),
            ]
        );

        return $record;
    }

    public function bulkUpsert(array $records): void
    {
        if (empty($records)) {
            return;
        }

        $this->model->upsert(
            $records,
            ['attendance_session_id', 'student_id'],
            ['attendance_status', 'checked_at', 'remark']
        );
    }

    public function bulkCreate(array $records): void
    {
        if (empty($records)) {
            return;
        }

        $this->model->insert($records);
    }

    public function delete(int $id): bool
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new \RuntimeException("Attendance record with ID {$id} not found.");
        }

        return (bool) $record->delete();
    }

    public function exists(int $sessionId, int $studentId): bool
    {
        return $this->model
            ->where('attendance_session_id', $sessionId)
            ->where('student_id', $studentId)
            ->exists();
    }

    public function countBySession(int $sessionId): int
    {
        return $this->model
            ->where('attendance_session_id', $sessionId)
            ->count();
    }
}
