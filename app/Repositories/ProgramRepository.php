<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\ProgramRepositoryInterface;
use App\Models\Program;
use Illuminate\Database\Eloquent\Collection;

class ProgramRepository implements ProgramRepositoryInterface
{
    public function __construct(
        private readonly Program $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with('department')->get();
    }

    public function findById(int $id): ?Program
    {
        return $this->model->with('department')->find($id);
    }

    public function create(array $data): Program
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Program
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("Program with ID {$id} not found.");
        }
        $record->update($data);

        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("Program with ID {$id} not found.");
        }

        return (bool) $record->delete();
    }
}
