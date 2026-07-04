<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\ProgramRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProgramService
{
    public function __construct(
        private readonly ProgramRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function findById(int $id): mixed
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): mixed
    {
        return DB::transaction(fn(): mixed => $this->repository->create($data));
    }

    public function update(int $id, array $data): mixed
    {
        return DB::transaction(fn(): mixed => $this->repository->update($id, $data));
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->repository->delete($id);
        });
    }
}
