<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\ExamSubject;
use Illuminate\Database\Eloquent\Collection;

interface ExamSubjectRepositoryInterface
{
    public function create(array $data): ExamSubject;
    public function update(int $id, array $data): ExamSubject;
    public function delete(int $id): bool;
    public function byExam(int $examId): Collection;
    public function byTeacher(int $teacherId): Collection;
    public function withMarks(int $id): ?ExamSubject;
    public function withSubject(int $id): ?ExamSubject;
}
