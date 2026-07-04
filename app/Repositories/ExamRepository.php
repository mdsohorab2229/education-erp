<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\ExamRepositoryInterface;
use App\Models\Exam;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ExamRepository implements ExamRepositoryInterface
{
    private const array EAGER_LOADS = [
        'examType',
        'academicYear',
        'semester',
        'department',
        'program',
        'shift',
        'section',
        'createdBy',
        'updatedBy',
    ];

    public function __construct(
        private readonly Exam $model
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(self::EAGER_LOADS)
            ->withCount('examSubjects')
            ->orderBy('start_date', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Exam
    {
        return $this->model->with(self::EAGER_LOADS)->find($id);
    }

    public function findWithRelations(int $id): ?Exam
    {
        return $this->model->with([...self::EAGER_LOADS, 'examSubjects', 'examSubjects.subject', 'examSubjects.teacher'])
            ->find($id);
    }

    public function create(array $data): Exam
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Exam
    {
        $exam = $this->model->find($id);
        if (!$exam) {
            throw new \RuntimeException("Exam with ID {$id} not found.");
        }
        $exam->update($data);

        return $exam;
    }

    public function delete(int $id): bool
    {
        $exam = $this->model->find($id);
        if (!$exam) {
            throw new \RuntimeException("Exam with ID {$id} not found.");
        }

        return (bool) $exam->delete();
    }

    public function published(): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('status', 'published')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function completed(): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('status', 'completed')
            ->orderBy('end_date', 'desc')
            ->get();
    }

    public function active(): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('status', 'published')
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->get();
    }

    public function upcoming(): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->whereIn('status', ['draft', 'published'])
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->get();
    }

    public function byAcademicYear(int $academicYearId): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('academic_year_id', $academicYearId)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function bySemester(int $semesterId): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('semester_id', $semesterId)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function byDepartment(int $departmentId): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('department_id', $departmentId)
            ->orderBy('start_date', 'desc')
            ->get();
    }
}
