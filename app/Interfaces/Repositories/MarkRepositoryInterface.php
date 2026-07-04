<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Mark;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MarkRepositoryInterface
{
    public function bulkUpsert(array $rows): void;
    public function update(int $id, array $data): Mark;
    public function delete(int $id): bool;
    public function byExam(int $examId): Collection;
    public function byExamPaginated(int $examId, int $perPage = 50): LengthAwarePaginator;
    public function byStudent(int $studentId): Collection;
    public function byExamSubject(int $examSubjectId): Collection;
    public function pendingApproval(): Collection;
    public function pendingApprovalPaginated(int $perPage = 50): LengthAwarePaginator;
    public function approved(): Collection;
    public function rejected(): Collection;
    public function withRelations(int $id): ?Mark;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
}
