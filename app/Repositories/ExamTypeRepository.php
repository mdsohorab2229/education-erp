<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\ExamTypeRepositoryInterface;
use App\Models\ExamType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ExamTypeRepository implements ExamTypeRepositoryInterface
{
    public function __construct(
        private readonly ExamType $model
    ) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): ExamType
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ExamType
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new \RuntimeException("ExamType with ID {$id} not found.");
        }
        $record->update($data);

        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new \RuntimeException("ExamType with ID {$id} not found.");
        }

        return (bool) $record->delete();
    }

    public function active(): Collection
    {
        return $this->model->where('status', 'active')->orderBy('name')->get();
    }
}
