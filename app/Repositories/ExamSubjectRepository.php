<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\ExamSubjectRepositoryInterface;
use App\Models\ExamSubject;
use Illuminate\Database\Eloquent\Collection;

class ExamSubjectRepository implements ExamSubjectRepositoryInterface
{
    private const array EAGER_LOADS = [
        'exam',
        'subject',
        'teacher',
    ];

    public function __construct(
        private readonly ExamSubject $model
    ) {}

    public function create(array $data): ExamSubject
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ExamSubject
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new \RuntimeException("ExamSubject with ID {$id} not found.");
        }
        $record->update($data);

        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->model->find($id);
        if (!$record) {
            throw new \RuntimeException("ExamSubject with ID {$id} not found.");
        }

        return (bool) $record->delete();
    }

    public function byExam(int $examId): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('exam_id', $examId)
            ->orderBy('id')
            ->get();
    }

    public function byTeacher(int $teacherId): Collection
    {
        return $this->model->with(self::EAGER_LOADS)
            ->where('teacher_id', $teacherId)
            ->orderBy('id')
            ->get();
    }

    public function withMarks(int $id): ?ExamSubject
    {
        return $this->model->with([...self::EAGER_LOADS, 'marks.student', 'marks.grade'])
            ->find($id);
    }

    public function withSubject(int $id): ?ExamSubject
    {
        return $this->model->with(['exam.section', 'subject'])->find($id);
    }
}
