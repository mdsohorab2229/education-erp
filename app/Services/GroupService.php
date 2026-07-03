<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\GroupRepositoryInterface;
use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GroupService
{
    public function __construct(
        private readonly GroupRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function findById(int $id): ?Group
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Group
    {
        return DB::transaction(fn(): Group => $this->repository->create($data));
    }

    public function update(int $id, array $data): Group
    {
        return DB::transaction(fn(): Group => $this->repository->update($id, $data));
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->repository->delete($id);
        });
    }
}
