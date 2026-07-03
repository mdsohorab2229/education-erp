<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\ShiftRepositoryInterface;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ShiftService
{
    public function __construct(
        private readonly ShiftRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function findById(int $id): ?Shift
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Shift
    {
        return DB::transaction(fn(): Shift => $this->repository->create($data));
    }

    public function update(int $id, array $data): Shift
    {
        return DB::transaction(fn(): Shift => $this->repository->update($id, $data));
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->repository->delete($id);
        });
    }
}
