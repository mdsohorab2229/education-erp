<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StudentRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Student;

    public function create(array $data): Student;

    public function update(int $id, array $data): Student;

    public function delete(int $id): bool;

    public function findByAdmissionNo(string $admissionNo): ?Student;

    public function findByIdWithRelations(int $id): ?Student;

    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function countByStatus(string $status): int;
}
