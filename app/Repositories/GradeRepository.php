<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\GradeRepositoryInterface;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Collection;

class GradeRepository implements GradeRepositoryInterface
{
    public function __construct(
        private readonly Grade $model
    ) {}

    public function allOrdered(): Collection
    {
        return $this->model->orderBy('min_mark')->get();
    }

    public function findGradeByMark(float $mark): ?Grade
    {
        return $this->model
            ->where('min_mark', '<=', $mark)
            ->where('max_mark', '>=', $mark)
            ->orderBy('min_mark', 'desc')
            ->first();
    }

    public function create(array $data): Grade
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Grade
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new \RuntimeException("Grade with ID {$id} not found.");
        }
        $record->update($data);

        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new \RuntimeException("Grade with ID {$id} not found.");
        }

        return (bool) $record->delete();
    }
}
