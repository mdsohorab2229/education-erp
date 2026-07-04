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

    public function generateEmployeeId(): string
    {
        $prefix = 'EMP-';
        $lastTeacher = $this->model
            ->where('employee_id', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTeacher) {
            $lastNumber = (int) substr($lastTeacher->employee_id, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function attachSubjects(int $teacherId, array $subjectIds): Teacher
    {
        $teacher = $this->findById($teacherId);
        if (!$teacher) {
            throw new \RuntimeException("Teacher with ID {$teacherId} not found.");
        }

        $existingIds = $teacher->subjects()->pluck('subject_id')->toArray();
        $duplicates = array_intersect($subjectIds, $existingIds);
        if (!empty($duplicates)) {
            throw new \RuntimeException('Some subjects are already assigned to this teacher.');
        }

        $teacher->subjects()->attach($subjectIds);

        return $teacher->load('subjects');
    }

    public function syncSubjects(int $teacherId, array $subjectIds): Teacher
    {
        $teacher = $this->findById($teacherId);
        if (!$teacher) {
            throw new \RuntimeException("Teacher with ID {$teacherId} not found.");
        }

        $teacher->subjects()->sync($subjectIds);

        return $teacher->load('subjects');
    }

    public function attachDepartments(int $teacherId, array $departmentIds): Teacher
    {
        $teacher = $this->findById($teacherId);
        if (!$teacher) {
            throw new \RuntimeException("Teacher with ID {$teacherId} not found.");
        }

        $existingIds = $teacher->departments()->pluck('department_id')->toArray();
        $duplicates = array_intersect($departmentIds, $existingIds);
        if (!empty($duplicates)) {
            throw new \RuntimeException('Some departments are already assigned to this teacher.');
        }

        $teacher->departments()->attach($departmentIds);

        return $teacher->load('departments');
    }

    public function syncDepartments(int $teacherId, array $departmentIds): Teacher
    {
        $teacher = $this->findById($teacherId);
        if (!$teacher) {
            throw new \RuntimeException("Teacher with ID {$teacherId} not found.");
        }

        $teacher->departments()->sync($departmentIds);

        return $teacher->load('departments');
    }

    public function countByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }
}
