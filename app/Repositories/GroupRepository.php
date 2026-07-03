<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\GroupRepositoryInterface;
use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

class GroupRepository implements GroupRepositoryInterface
{
    public function __construct(
        private readonly Group $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with('program')->get();
    }

    public function findById(int $id): ?Group
    {
        return $this->model->with('program')->find($id);
    }

    public function create(array $data): Group
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Group
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("Group with ID {$id} not found.");
        }
        $record->update($data);

        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("Group with ID {$id} not found.");
        }

        return (bool) $record->delete();
    }
}
