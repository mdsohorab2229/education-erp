<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\ExamSubjectRepositoryInterface;
use App\Interfaces\Repositories\MarkRepositoryInterface;
use App\Models\Mark;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MarksEntryService
{
    public function __construct(
        private readonly MarkRepositoryInterface $markRepository,
        private readonly ExamSubjectRepositoryInterface $examSubjectRepository,
        private readonly GradeCalculationService $gradeCalculationService,
    ) {}

    public function bulkStore(array $rows): array
    {
        return DB::transaction(function () use ($rows): array {
            $processed = [];

            foreach ($rows as $row) {
                $totalMark = $this->calculateTotal($row);
                $grade = $this->gradeCalculationService->calculate($totalMark);

                $processed[] = array_merge($row, [
                    'total_mark' => $totalMark,
                    'grade_id' => $grade['grade_id'],
                ]);
            }

            $this->markRepository->bulkUpsert($processed);

            return $processed;
        });
    }

    public function updateMark(int $id, array $data): Mark
    {
        return DB::transaction(function () use ($id, $data): Mark {
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

    public function loadStudents(int $examSubjectId): array
    {
        $examSubject = $this->examSubjectRepository->withSubject($examSubjectId);

        if (!$examSubject) {
            throw new \RuntimeException("Exam subject with ID {$examSubjectId} not found.");
        }

        $examSubject->load('exam.section');
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
