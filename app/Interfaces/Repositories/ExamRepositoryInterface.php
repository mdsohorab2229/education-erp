<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Exam;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ExamRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Exam;
    public function findWithRelations(int $id): ?Exam;
    public function create(array $data): Exam;
    public function update(int $id, array $data): Exam;
    public function delete(int $id): bool;
    public function published(): Collection;
    public function completed(): Collection;
    public function active(): Collection;
    public function upcoming(): Collection;
    public function byAcademicYear(int $academicYearId): Collection;
    public function bySemester(int $semesterId): Collection;
    public function byDepartment(int $departmentId): Collection;
}
