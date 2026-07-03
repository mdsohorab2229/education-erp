<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\GuardianRepositoryInterface;
use App\Interfaces\Repositories\StudentRepositoryInterface;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StudentService
{
    public function __construct(
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly GuardianRepositoryInterface $guardianRepository,
    ) {}

    public function all(): Collection
    {
        return $this->studentRepository->all();
    }

    public function findById(int $id): ?Student
    {
        return $this->studentRepository->findByIdWithRelations($id);
    }

    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->studentRepository->paginateWithFilters($filters, $perPage);
    }

    public function admit(array $data): Student
    {
        return DB::transaction(function () use ($data): Student {
            $data['admission_no'] = $this->generateAdmissionNo();

            $student = $this->studentRepository->create($data);

            if (!empty($data['guardian'])) {
                $data['guardian']['student_id'] = $student->id;
                $this->guardianRepository->create($data['guardian']);
            }

            return $student->load([
                'guardian', 'program', 'section', 'academicYear', 'shift', 'group',
            ]);
        });
    }

    public function update(int $id, array $data): Student
    {
        return DB::transaction(function () use ($id, $data): Student {
            $student = $this->studentRepository->update($id, $data);

            if (!empty($data['guardian'])) {
                $existing = $this->guardianRepository->findByStudentId($student->id);
                if ($existing) {
                    $this->guardianRepository->updateByStudentId($student->id, $data['guardian']);
                } else {
                    $data['guardian']['student_id'] = $student->id;
                    $this->guardianRepository->create($data['guardian']);
                }
            }

            return $student;
        });
    }

    public function changeStatus(int $id, string $status): Student
    {
        return DB::transaction(function () use ($id, $status): Student {
            return $this->studentRepository->update($id, ['status' => $status]);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->studentRepository->delete($id);
        });
    }

    public function generateAdmissionNo(): string
    {
        $year = now()->format('Y');
        $prefix = "STU-{$year}-";
        $lastStudent = DB::table('students')
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

    public function countByStatus(string $status): int
    {
        return $this->studentRepository->countByStatus($status);
    }
}
