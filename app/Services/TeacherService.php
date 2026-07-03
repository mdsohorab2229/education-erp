<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\TeacherRepositoryInterface;
use App\Models\Teacher;
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

    public function findById(int $id): ?Teacher
    {
        return $this->teacherRepository->findByIdWithRelations($id);
    }

    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->teacherRepository->paginateWithFilters($filters, $perPage);
    }

    public function create(array $data): Teacher
    {
        return DB::transaction(function () use ($data): Teacher {
            return $this->teacherRepository->create($data);
        });
    }

    public function update(int $id, array $data): Teacher
    {
        return DB::transaction(function () use ($id, $data): Teacher {
            return $this->teacherRepository->update($id, $data);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->teacherRepository->delete($id);
        });
    }

    public function assignSubjects(int $teacherId, array $subjectIds): Teacher
    {
        return DB::transaction(function () use ($teacherId, $subjectIds): Teacher {
            $teacher = $this->teacherRepository->findById($teacherId);
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
        });
    }

    public function syncSubjects(int $teacherId, array $subjectIds): Teacher
    {
        return DB::transaction(function () use ($teacherId, $subjectIds): Teacher {
            $teacher = $this->teacherRepository->findById($teacherId);
            if (!$teacher) {
                throw new \RuntimeException("Teacher with ID {$teacherId} not found.");
            }

            $teacher->subjects()->sync($subjectIds);

            return $teacher->load('subjects');
        });
    }

    public function assignDepartments(int $teacherId, array $departmentIds): Teacher
    {
        return DB::transaction(function () use ($teacherId, $departmentIds): Teacher {
            $teacher = $this->teacherRepository->findById($teacherId);
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
        });
    }

    public function syncDepartments(int $teacherId, array $departmentIds): Teacher
    {
        return DB::transaction(function () use ($teacherId, $departmentIds): Teacher {
            $teacher = $this->teacherRepository->findById($teacherId);
            if (!$teacher) {
                throw new \RuntimeException("Teacher with ID {$teacherId} not found.");
            }

            $teacher->departments()->sync($departmentIds);

            return $teacher->load('departments');
        });
    }

    public function generateEmployeeId(): string
    {
        $prefix = 'EMP-';
        $lastTeacher = DB::table('teachers')
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

    public function countByStatus(string $status): int
    {
        return $this->teacherRepository->countByStatus($status);
    }
}
