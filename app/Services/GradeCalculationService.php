<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\GradeNotFoundException;
use App\Interfaces\Repositories\GradeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GradeCalculationService
{
    private ?Collection $grades = null;

    public function __construct(
        private readonly GradeRepositoryInterface $gradeRepository,
    ) {}

    public function calculate(float $mark): array
    {
        $grades = $this->getGrades();

        $grade = $grades->first(fn ($g) => $mark >= $g->min_mark && $mark <= $g->max_mark);

        if (!$grade) {
            throw new GradeNotFoundException("No grade found for mark {$mark}.");
        }

        return [
            'grade_id' => $grade->id,
            'grade_name' => $grade->grade_name,
            'grade_letter' => $grade->grade_letter,
            'gpa' => (float) $grade->gpa_point,
        ];
    }

    public function calculateCollection(array $marks): array
    {
        $grades = $this->getGrades();
        $results = [];

        foreach ($marks as $key => $markData) {
            $totalMark = (float) ($markData['total_mark'] ?? $markData['obtained_mark'] ?? 0);

            $grade = $grades->first(fn ($g) => $totalMark >= $g->min_mark && $totalMark <= $g->max_mark);

            if (!$grade) {
                throw new GradeNotFoundException("No grade found for total mark {$totalMark}.");
            }

            $results[$key] = [
                'grade_id' => $grade->id,
                'grade_name' => $grade->grade_name,
                'grade_letter' => $grade->grade_letter,
                'gpa' => (float) $grade->gpa_point,
            ];
        }

        return $results;
    }

    private function getGrades(): Collection
    {
        if ($this->grades === null) {
            $this->grades = $this->gradeRepository->allOrdered();
        }

        return $this->grades;
    }
}
