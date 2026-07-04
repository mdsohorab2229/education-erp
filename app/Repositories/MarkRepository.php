<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\MarkRepositoryInterface;
use App\Models\Mark;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MarkRepository implements MarkRepositoryInterface
{
    private const array EAGER_LOADS = [
        'examSubject.exam',
        'examSubject.subject',
        'student',
        'grade',
        'approvedBy',
    ];

    private const array UNIQUE_BY = [
        'exam_subject_id',
        'student_id',
    ];

    private const array UPDATE_COLUMNS = [
        'obtained_mark',
        'practical_mark',
        'viva_mark',
        'total_mark',
        'grade_id',
        'approval_status',
        'approved_by',
        'approved_at',
        'remark',
        'updated_by',
    ];

    public function __construct(
        private readonly Mark $model
    ) {}

    public function bulkUpsert(array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        $this->model->upsert($rows, self::UNIQUE_BY, self::UPDATE_COLUMNS);
    }

    public function update(int $id, array $data): Mark
    {
        $mark = $this->model->find($id);
        if (!$mark) {
            throw new \RuntimeException("Mark with ID {$id} not found.");
        }
        $mark->update($data);

        return $mark;
    }

    public function delete(int $id): bool
    {
        $mark = $this->model->find($id);
        if (!$mark) {
            throw new \RuntimeException("Mark with ID {$id} not found.");
        }

        return (bool) $mark->delete();
    }

    public function byExam(int $examId): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->whereHas('examSubject', fn ($q) => $q->where('exam_id', $examId))
            ->orderBy('id')
            ->get();
    }

    public function byStudent(int $studentId): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function byExamSubject(int $examSubjectId): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('exam_subject_id', $examSubjectId)
            ->orderBy('id')
            ->get();
    }

    public function pendingApproval(): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('approval_status', 'pending')
            ->orderBy('created_at')
            ->get();
    }

    public function approved(): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('approval_status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->get();
    }

    public function rejected(): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('approval_status', 'rejected')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function withRelations(int $id): ?Mark
    {
        return $this->model->with([...self::EAGER_LOADS, 'createdBy', 'updatedBy'])
            ->find($id);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(self::EAGER_LOADS)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
