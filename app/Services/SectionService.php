<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\SectionRepositoryInterface;
use App\Models\Section;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SectionService
{
    public function __construct(
        private readonly SectionRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function findById(int $id): ?Section
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Section
    {
        return DB::transaction(fn(): Section => $this->repository->create($data));
    }

    public function update(int $id, array $data): Section
    {
        return DB::transaction(fn(): Section => $this->repository->update($id, $data));
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->repository->delete($id);
        });
    }
}
