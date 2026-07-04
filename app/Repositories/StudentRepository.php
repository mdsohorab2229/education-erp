<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\StudentRepositoryInterface;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class StudentRepository implements StudentRepositoryInterface
{
    public function __construct(
        private readonly Student $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with(['program', 'section', 'academicYear', 'shift'])->get();
    }

    public function findById(int $id): ?Student
    {
        return $this->model->find($id);
    }

    public function create(array $data): Student
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Student
    {
        $student = $this->findById($id);
        if (!$student) {
            throw new \RuntimeException("Student with ID {$id} not found.");
        }
        $student->update($data);

        return $student;
    }

    public function delete(int $id): bool
    {
        $student = $this->findById($id);
        if (!$student) {
            throw new \RuntimeException("Student with ID {$id} not found.");
        }

        return (bool) $student->delete();
    }

    public function findByAdmissionNo(string $admissionNo): ?Student
    {
        return $this->model->where('admission_no', $admissionNo)->first();
    }

    public function findByEmail(string $email): ?Student
    {
        return $this->model->where('email', $email)->first();
    }

    public function generateAdmissionNo(): string
    {
        $year = now()->format('Y');
        $prefix = "STU-{$year}-";
        $lastStudent = $this->model
            ->where('admission_no', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->admission_no, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad((string) $newNumber, 6, '0', STR_PAD_LEFT);
    }

    public function findByIdWithRelations(int $id): ?Student
    {
        return $this->model->with([
            'program.department',
            'section',
            'academicYear',
            'shift',
            'group',
            'guardian',
            'documents',
            'promotions',
        ])->find($id);
    }

    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['program', 'section', 'academicYear', 'shift']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

        if (!empty($filters['section_id'])) {
            $query->where('section_id', $filters['section_id']);
        }

        if (!empty($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }

        if (!empty($filters['shift_id'])) {
            $query->where('shift_id', $filters['shift_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_no', 'like', "%{$search}%")
                    ->orWhere('roll_no', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function countByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }
}
