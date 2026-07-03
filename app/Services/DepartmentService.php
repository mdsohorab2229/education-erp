<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\DepartmentRepositoryInterface;
use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
    public function __construct(
        private readonly DepartmentRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function findById(int $id): ?Department
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Department
    {
        return DB::transaction(fn(): Department => $this->repository->create($data));
    }

    public function update(int $id, array $data): Department
    {
        return DB::transaction(fn(): Department => $this->repository->update($id, $data));
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->repository->delete($id);
        });
    }
}
