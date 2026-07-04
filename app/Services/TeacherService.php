<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\TeacherRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TeacherService
{
    public function __construct(
        private readonly TeacherRepositoryInterface $teacherRepository,
    ) {}

    public function all(): Collection
    {
        return $this->teacherRepository->all();
    }

    public function findById(int $id): mixed
    {
        return $this->teacherRepository->findByIdWithRelations($id);
    }

    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->teacherRepository->paginateWithFilters($filters, $perPage);
    }

    public function create(array $data): mixed
    {
        return DB::transaction(function () use ($data): mixed {
            return $this->teacherRepository->create($data);
        });
    }

    public function update(int $id, array $data): mixed
    {
        return DB::transaction(function () use ($id, $data): mixed {
            return $this->teacherRepository->update($id, $data);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->teacherRepository->delete($id);
        });
    }

    public function assignSubjects(int $teacherId, array $subjectIds): mixed
    {
        return DB::transaction(function () use ($teacherId, $subjectIds): mixed {
            return $this->teacherRepository->attachSubjects($teacherId, $subjectIds);
        });
    }

    public function syncSubjects(int $teacherId, array $subjectIds): mixed
    {
        return DB::transaction(function () use ($teacherId, $subjectIds): mixed {
            return $this->teacherRepository->syncSubjects($teacherId, $subjectIds);
        });
    }

    public function assignDepartments(int $teacherId, array $departmentIds): mixed
    {
        return DB::transaction(function () use ($teacherId, $departmentIds): mixed {
            return $this->teacherRepository->attachDepartments($teacherId, $departmentIds);
        });
    }

    public function syncDepartments(int $teacherId, array $departmentIds): mixed
    {
        return DB::transaction(function () use ($teacherId, $departmentIds): mixed {
            return $this->teacherRepository->syncDepartments($teacherId, $departmentIds);
        });
    }

    public function generateEmployeeId(): string
    {
        return $this->teacherRepository->generateEmployeeId();
    }

    public function countByStatus(string $status): int
    {
        return $this->teacherRepository->countByStatus($status);
    }
}
