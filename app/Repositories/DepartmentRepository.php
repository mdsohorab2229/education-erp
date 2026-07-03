<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\DepartmentRepositoryInterface;
use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function __construct(
        private readonly Department $model
    ) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Department
    {
        return $this->model->find($id);
    }

    public function create(array $data): Department
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Department
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("Department with ID {$id} not found.");
        }
        $record->update($data);

        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("Department with ID {$id} not found.");
        }

        return (bool) $record->delete();
    }
}
