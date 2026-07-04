<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\AssignmentSubmissionRepositoryInterface;
use App\Models\AssignmentSubmission;
use Illuminate\Database\Eloquent\Collection;

class AssignmentSubmissionRepository implements AssignmentSubmissionRepositoryInterface
{
    public function __construct(
        private readonly AssignmentSubmission $model,
    ) {}

    public function findById(int $id): ?AssignmentSubmission
    {
        return $this->model->with(['assignment', 'student'])->find($id);
    }

    public function findByAssignmentAndStudent(int $assignmentId, int $studentId): ?AssignmentSubmission
    {
        return $this->model->where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();
    }

    public function create(array $data): AssignmentSubmission
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): AssignmentSubmission
    {
        $submission = $this->findById($id);
        if (!$submission) {
            throw new \RuntimeException("Submission with ID {$id} not found.");
        }
        $submission->update($data);

        return $submission;
    }

    public function delete(int $id): bool
    {
        $submission = $this->findById($id);
        if (!$submission) {
            throw new \RuntimeException("Submission with ID {$id} not found.");
        }

        return (bool) $submission->delete();
    }

    public function getByAssignment(int $assignmentId): Collection
    {
        return $this->model->with(['student'])
            ->where('assignment_id', $assignmentId)
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    public function getByStudent(int $studentId): Collection
    {
        return $this->model->with(['assignment'])
            ->where('student_id', $studentId)
            ->orderBy('submitted_at', 'desc')
            ->get();
    }
}
