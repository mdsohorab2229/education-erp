<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\AcademicYearRepositoryInterface;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Collection;

class AcademicYearRepository implements AcademicYearRepositoryInterface
{
    public function __construct(
        private readonly AcademicYear $model
    ) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?AcademicYear
    {
        return $this->model->find($id);
    }

    public function create(array $data): AcademicYear
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): AcademicYear
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("AcademicYear with ID {$id} not found.");
        }
        $record->update($data);

        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("AcademicYear with ID {$id} not found.");
        }

        return (bool) $record->delete();
    }
}
