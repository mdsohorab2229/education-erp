<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\AssignmentRepositoryInterface;
use App\Models\Assignment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AssignmentRepository implements AssignmentRepositoryInterface
{
    private const array EAGER_LOADS = [
        'teacher',
        'subject',
        'section',
    ];

    public function __construct(
        private readonly Assignment $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with(self::EAGER_LOADS)->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(self::EAGER_LOADS)->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Assignment
    {
        return $this->model->with(self::EAGER_LOADS)->find($id);
    }

    public function create(array $data): Assignment
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Assignment
    {
        $assignment = $this->model->find($id);
        if (!$assignment) {
            throw new \RuntimeException("Assignment with ID {$id} not found.");
        }
        $assignment->update($data);

        return $assignment;
    }

    public function delete(int $id): bool
    {
        $assignment = $this->model->find($id);
        if (!$assignment) {
            throw new \RuntimeException("Assignment with ID {$id} not found.");
        }

        return (bool) $assignment->delete();
    }

    public function getBySection(int $sectionId, ?string $status = null): Collection
    {
        $query = $this->model->with(self::EAGER_LOADS)
            ->where('section_id', $sectionId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('due_date', 'desc')->get();
    }

    public function getByTeacher(int $teacherId, ?string $status = null): Collection
    {
        $query = $this->model->with(self::EAGER_LOADS)
            ->where('teacher_id', $teacherId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('due_date', 'desc')->get();
    }

    public function getBySubject(int $subjectId): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('subject_id', $subjectId)
            ->orderBy('due_date', 'desc')
            ->get();
    }

    public function getUpcoming(int $sectionId): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('section_id', $sectionId)
            ->where('due_date', '>=', now()->toDateString())
            ->orderBy('due_date', 'asc')
            ->get();
    }
}
