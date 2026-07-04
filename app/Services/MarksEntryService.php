<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\ExamSubjectRepositoryInterface;
use App\Interfaces\Repositories\MarkRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MarksEntryService
{
    public function __construct(
        private readonly MarkRepositoryInterface $markRepository,
        private readonly ExamSubjectRepositoryInterface $examSubjectRepository,
        private readonly GradeCalculationService $gradeCalculationService,
    ) {}

    public function bulkStore(array $data): array
    {
        $userId = (int) ($data['user_id'] ?? 0);
        $examSubjectId = (int) ($data['exam_subject_id'] ?? 0);
        $rows = $data['marks'] ?? [];

        $processed = array_map(
            fn (array $mark): array => array_merge($mark, [
                'exam_subject_id' => $examSubjectId,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]),
            $rows,
        );

        return DB::transaction(function () use ($processed): array {
            $enriched = [];

            foreach ($processed as $row) {
                $totalMark = $this->calculateTotal($row);
                $grade = $this->gradeCalculationService->calculate($totalMark);

                $enriched[] = array_merge($row, [
                    'total_mark' => $totalMark,
                    'grade_id' => $grade['grade_id'],
                ]);
            }

            $this->markRepository->bulkUpsert($enriched);

            return $enriched;
        });
    }

    public function updateMark(int $id, array $data): mixed
    {
        $userId = (int) ($data['user_id'] ?? 0);
        unset($data['user_id']);
        $data['updated_by'] = $userId;

        return DB::transaction(function () use ($id, $data): mixed {
            $mark = $this->markRepository->update($id, $data);

            $totalMark = $this->calculateTotal($mark->toArray());
            $grade = $this->gradeCalculationService->calculate($totalMark);

            return $this->markRepository->update($id, [
                'total_mark' => $totalMark,
                'grade_id' => $grade['grade_id'],
            ]);
        });
    }

    public function recalculate(int $examSubjectId): Collection
    {
        return DB::transaction(function () use ($examSubjectId): Collection {
            $marks = $this->markRepository->byExamSubject($examSubjectId);
            $updates = [];

            foreach ($marks as $mark) {
                $totalMark = $this->calculateTotal($mark->toArray());
                $grade = $this->gradeCalculationService->calculate($totalMark);

                $updates[] = [
                    'id' => $mark->id,
                    'exam_subject_id' => $mark->exam_subject_id,
                    'student_id' => $mark->student_id,
                    'obtained_mark' => $mark->obtained_mark,
                    'practical_mark' => $mark->practical_mark,
                    'viva_mark' => $mark->viva_mark,
                    'total_mark' => $totalMark,
                    'grade_id' => $grade['grade_id'],
                    'updated_by' => $mark->updated_by,
                ];
            }

            $this->markRepository->bulkUpsert($updates);

            return $this->markRepository->byExamSubject($examSubjectId);
        });
    }

    public function getStudentMarks(int $studentId): Collection
    {
        return $this->markRepository->byStudent($studentId);
    }

    public function getExamMarks(int $examId): Collection
    {
        return $this->markRepository->byExam($examId);
    }

    public function getExamMarksPaginated(int $examId, int $perPage = 50): LengthAwarePaginator
    {
        return $this->markRepository->byExamPaginated($examId, $perPage);
    }

    public function loadStudents(int $examSubjectId): array
    {
        $examSubject = $this->examSubjectRepository->withSubject($examSubjectId);

        if (!$examSubject) {
            throw new \RuntimeException(__('examination.exam_subject_not_found'));
        }

        $marks = $this->markRepository->byExamSubject($examSubjectId);

        return [
            'exam_subject' => $examSubject,
            'marks' => $marks,
        ];
    }

    private function calculateTotal(array $data): float
    {
        $obtained = (float) ($data['obtained_mark'] ?? 0);
        $practical = (float) ($data['practical_mark'] ?? 0);
        $viva = (float) ($data['viva_mark'] ?? 0);

        return round($obtained + $practical + $viva, 2);
    }
}
