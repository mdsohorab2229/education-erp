<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\AssignmentSubmission;
use Illuminate\Database\Eloquent\Collection;

interface AssignmentSubmissionRepositoryInterface
{
    public function findById(int $id): ?AssignmentSubmission;

    public function findByAssignmentAndStudent(int $assignmentId, int $studentId): ?AssignmentSubmission;

    public function create(array $data): AssignmentSubmission;

    public function update(int $id, array $data): AssignmentSubmission;

    public function delete(int $id): bool;

    public function getByAssignment(int $assignmentId): Collection;

    public function getByStudent(int $studentId): Collection;
}
