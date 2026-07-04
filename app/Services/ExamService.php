<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\ExamRepositoryInterface;
use App\Models\Exam;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ExamService
{
    public function __construct(
        private readonly ExamRepositoryInterface $repository,
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function findById(int $id): ?Exam
    {
        return $this->repository->findById($id);
    }

    public function findWithRelations(int $id): ?Exam
    {
        return $this->repository->findWithRelations($id);
    }

    public function create(array $data): Exam
    {
        return DB::transaction(fn(): Exam => $this->repository->create($data));
    }

    public function update(int $id, array $data): Exam
    {
        return DB::transaction(fn(): Exam => $this->repository->update($id, $data));
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->repository->delete($id);
        });
    }
}
