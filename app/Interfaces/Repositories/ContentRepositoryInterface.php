<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Content;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ContentRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Content;

    public function create(array $data): Content;

    public function update(int $id, array $data): Content;

    public function delete(int $id): bool;

    public function getBySection(int $sectionId, ?string $type = null): Collection;

    public function getByTeacher(int $teacherId, ?string $type = null): Collection;

    public function getBySubject(int $subjectId): Collection;
}
