<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\SubjectRepositoryInterface;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SubjectService
{
    public function __construct(
        private readonly SubjectRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function findById(int $id): ?Subject
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Subject
    {
        return DB::transaction(fn(): Subject => $this->repository->create($data));
    }

    public function update(int $id, array $data): Subject
    {
        return DB::transaction(fn(): Subject => $this->repository->update($id, $data));
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->repository->delete($id);
        });
    }
}
