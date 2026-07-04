<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Teacher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TeacherRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Teacher;

    public function findByIdWithRelations(int $id): ?Teacher;

    public function create(array $data): Teacher;

    public function update(int $id, array $data): Teacher;

    public function delete(int $id): bool;

    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findByEmployeeId(string $employeeId): ?Teacher;

    public function generateEmployeeId(): string;

    public function attachSubjects(int $teacherId, array $subjectIds): Teacher;

    public function syncSubjects(int $teacherId, array $subjectIds): Teacher;

    public function attachDepartments(int $teacherId, array $departmentIds): Teacher;

    public function syncDepartments(int $teacherId, array $departmentIds): Teacher;

    public function countByStatus(string $status): int;
}
