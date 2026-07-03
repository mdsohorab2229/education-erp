<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\ShiftRepositoryInterface;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Collection;

class ShiftRepository implements ShiftRepositoryInterface
{
    public function __construct(
        private readonly Shift $model
    ) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Shift
    {
        return $this->model->find($id);
    }

    public function create(array $data): Shift
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Shift
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("Shift with ID {$id} not found.");
        }
        $record->update($data);

        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("Shift with ID {$id} not found.");
        }

        return (bool) $record->delete();
    }
}
