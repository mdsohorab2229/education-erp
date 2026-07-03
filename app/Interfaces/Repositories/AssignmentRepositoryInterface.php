<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Assignment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AssignmentRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Assignment;

    public function create(array $data): Assignment;

    public function update(int $id, array $data): Assignment;

    public function delete(int $id): bool;

    public function getBySection(int $sectionId, ?string $status = null): Collection;

    public function getByTeacher(int $teacherId, ?string $status = null): Collection;

    public function getBySubject(int $subjectId): Collection;

    public function getUpcoming(int $sectionId): Collection;
}
