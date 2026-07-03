<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\ProgramRepositoryInterface;
use App\Models\Program;
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

    public function findById(int $id): ?Program
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Program
    {
        return DB::transaction(fn(): Program => $this->repository->create($data));
    }

    public function update(int $id, array $data): Program
    {
        return DB::transaction(fn(): Program => $this->repository->update($id, $data));
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->repository->delete($id);
        });
    }
}
