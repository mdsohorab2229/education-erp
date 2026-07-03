<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\SubjectRepositoryInterface;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;

class SubjectRepository implements SubjectRepositoryInterface
{
    public function __construct(
        private readonly Subject $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with('program')->get();
    }

    public function findById(int $id): ?Subject
    {
        return $this->model->with('program')->find($id);
    }

    public function create(array $data): Subject
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Subject
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("Subject with ID {$id} not found.");
        }
        $record->update($data);

        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("Subject with ID {$id} not found.");
        }

        return (bool) $record->delete();
    }
}
