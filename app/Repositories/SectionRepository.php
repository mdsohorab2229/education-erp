<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\SectionRepositoryInterface;
use App\Models\Section;
use Illuminate\Database\Eloquent\Collection;

class SectionRepository implements SectionRepositoryInterface
{
    public function __construct(
        private readonly Section $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with('program')->get();
    }

    public function findById(int $id): ?Section
    {
        return $this->model->with('program')->find($id);
    }

    public function create(array $data): Section
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Section
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("Section with ID {$id} not found.");
        }
        $record->update($data);

        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->findById($id);
        if (!$record) {
            throw new \RuntimeException("Section with ID {$id} not found.");
        }

        return (bool) $record->delete();
    }
}
