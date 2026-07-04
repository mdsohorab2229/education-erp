<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\GuardianRepositoryInterface;
use App\Interfaces\Repositories\StudentRepositoryInterface;
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

    public function findById(int $id): mixed
    {
        return $this->studentRepository->findByIdWithRelations($id);
    }

    public function paginateWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->studentRepository->paginateWithFilters($filters, $perPage);
    }

    public function admit(array $data): mixed
    {
        return DB::transaction(function () use ($data): mixed {
            $data['admission_no'] = $this->studentRepository->generateAdmissionNo();

            $student = $this->studentRepository->create($data);

            if (!empty($data['guardian'])) {
                $data['guardian']['student_id'] = $student->id;
                $this->guardianRepository->create($data['guardian']);
            }

            return $student;
        });
    }

    public function update(int $id, array $data): mixed
    {
        return DB::transaction(function () use ($id, $data): mixed {
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

    public function changeStatus(int $id, string $status): mixed
    {
        return DB::transaction(function () use ($id, $status): mixed {
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
        return $this->studentRepository->generateAdmissionNo();
    }

    public function countByStatus(string $status): int
    {
        return $this->studentRepository->countByStatus($status);
    }
}
