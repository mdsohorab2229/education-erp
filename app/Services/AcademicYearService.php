<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\AcademicYearRepositoryInterface;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AcademicYearService
{
    public function __construct(
        private readonly AcademicYearRepositoryInterface $repository
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function findById(int $id): ?AcademicYear
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): AcademicYear
    {
        return DB::transaction(function () use ($data): AcademicYear {
            if (!empty($data['is_current'])) {
                $this->repository->all()->each->update(['is_current' => false]);
            }

            return $this->repository->create($data);
        });
    }

    public function update(int $id, array $data): AcademicYear
    {
        return DB::transaction(function () use ($id, $data): AcademicYear {
            if (!empty($data['is_current'])) {
                $this->repository->all()->each->update(['is_current' => false]);
            }

            return $this->repository->update($id, $data);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->repository->delete($id);
        });
    }
}
