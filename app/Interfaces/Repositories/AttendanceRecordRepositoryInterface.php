<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\AttendanceRecord;
use Illuminate\Database\Eloquent\Collection;

interface AttendanceRecordRepositoryInterface
{
    public function getBySession(int $sessionId): Collection;

    public function getBySessionWithStudent(int $sessionId): Collection;

    public function getStudentAttendance(int $sessionId, int $studentId): ?AttendanceRecord;

    public function upsert(array $data): AttendanceRecord;

    public function bulkUpsert(array $records): void;

    public function bulkCreate(array $records): void;

    public function delete(int $id): bool;

    public function exists(int $sessionId, int $studentId): bool;

    public function countBySession(int $sessionId): int;
}
