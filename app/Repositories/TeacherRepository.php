<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\TeacherRepositoryInterface;
use App\Models\Teacher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TeacherRepository implements TeacherRepositoryInterface
{
    public function __construct(
        private readonly Teacher $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with(['departments', 'subjects'])->get();
    }

    public function findById(int $id): ?Teacher
    {
        return $this->model->find($id);
    }

    public function findByIdWithRelations(int $id): ?Teacher
    {
        return $this->model->with([
            'departments',
            'subjects',
            'qualifications',
            'user',
        ])->find($id);
    }

    public function create(array $data): Teacher
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Teacher
    {
        $teacher = $this->findById($id);
        if (!$teacher) {
            throw new \RuntimeException("Teacher with ID {$id} not found.");
        }
        $teacher->update($data);

        return $teacher;
    }

    public function delete(int $id): bool
    {
        $teacher = $this->findById($id);
        if (!$teacher) {
            throw new \RuntimeException("Teacher with ID {$id} not found.");
        }

        return (bool) $teacher->delete();
    }

    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['departments', 'subjects']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['department_id'])) {
            $query->whereHas('departments', fn ($q) => $q->where('department_id', $filters['department_id']));
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findByEmployeeId(string $employeeId): ?Teacher
    {
        return $this->model->where('employee_id', $employeeId)->first();
    }

    public function countByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }
}
