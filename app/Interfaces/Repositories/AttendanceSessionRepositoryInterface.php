<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\AttendanceSession;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AttendanceSessionRepositoryInterface
{
    public function findById(int $id): ?AttendanceSession;

    public function findByIdWithRelations(int $id): ?AttendanceSession;

    public function findSessionByFilters(array $filters): ?AttendanceSession;

    public function create(array $data): AttendanceSession;

    public function findOrCreate(array $filters, array $data): AttendanceSession;

    public function updateSummary(int $sessionId, array $summary): AttendanceSession;

    public function updateStatus(int $sessionId, string $status): AttendanceSession;

    public function exists(array $filters): bool;

    public function paginateHistory(array $filters, int $perPage = 15): LengthAwarePaginator;
}
