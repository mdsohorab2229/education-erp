<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\ExamRepositoryInterface;
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

    public function findById(int $id): mixed
    {
        return $this->repository->findById($id);
    }

    public function findWithRelations(int $id): mixed
    {
        return $this->repository->findWithRelations($id);
    }

    public function create(array $data): mixed
    {
        return DB::transaction(function () use ($data): mixed {
            $userId = (int) ($data['user_id'] ?? 0);
            unset($data['user_id']);
            $data['created_by'] = $userId;
            $data['updated_by'] = $userId;

            return $this->repository->create($data);
        });
    }

    public function update(int $id, array $data): mixed
    {
        return DB::transaction(function () use ($id, $data): mixed {
            if (isset($data['user_id'])) {
                $data['updated_by'] = (int) $data['user_id'];
                unset($data['user_id']);
            }

            return $this->repository->update($id, $data);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->repository->delete($id);
        });
    }
}
