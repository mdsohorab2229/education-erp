<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\AssignmentRepositoryInterface;
use App\Interfaces\Repositories\AssignmentSubmissionRepositoryInterface;
use App\Interfaces\Repositories\StudentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AssignmentService
{
    private const string DISK = 'public';

    private const string SUBMISSION_PATH = 'assignments/submissions';

    public function __construct(
        private readonly AssignmentRepositoryInterface $repository,
        private readonly AssignmentSubmissionRepositoryInterface $submissionRepository,
        private readonly StudentRepositoryInterface $studentRepository,
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function findById(int $id): mixed
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): mixed
    {
        return DB::transaction(fn(): mixed => $this->repository->create($data));
    }

    public function update(int $id, array $data): mixed
    {
        return DB::transaction(fn(): mixed => $this->repository->update($id, $data));
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->repository->delete($id);
        });
    }

    public function getBySection(int $sectionId, ?string $status = null): Collection
    {
        return $this->repository->getBySection($sectionId, $status);
    }

    public function getByTeacher(int $teacherId, ?string $status = null): Collection
    {
        return $this->repository->getByTeacher($teacherId, $status);
    }

    public function getUpcoming(int $sectionId): Collection
    {
        return $this->repository->getUpcoming($sectionId);
    }

    public function findStudentIdByEmail(string $email): ?int
    {
        $student = $this->studentRepository->findByEmail($email);

        return $student?->id;
    }

    public function submit(int $assignmentId, int $studentId, UploadedFile $file): mixed
    {
        $assignment = $this->repository->findById($assignmentId);

        if (!$assignment) {
            throw new \RuntimeException("Assignment with ID {$assignmentId} not found.");
        }

        $student = $this->studentRepository->findById($studentId);

        if (!$student) {
            throw new \RuntimeException("Student with ID {$studentId} not found.");
        }

        $existing = $this->submissionRepository->findByAssignmentAndStudent($assignmentId, $studentId);

        if ($existing) {
            throw ValidationException::withMessages([
                'assignment_id' => ['A submission already exists for this assignment and student.'],
            ]);
        }

        return DB::transaction(function () use ($assignment, $studentId, $file): mixed {
            $submissionFile = $file->store(self::SUBMISSION_PATH, self::DISK);

            return $this->submissionRepository->create([
                'assignment_id' => $assignment->id,
                'student_id' => $studentId,
                'submission_file' => $submissionFile,
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);
        });
    }

    public function updateMarks(int $submissionId, ?float $marks, ?string $feedback = null): mixed
    {
        $submission = $this->submissionRepository->findById($submissionId);

        if (!$submission) {
            throw new \RuntimeException("Submission with ID {$submissionId} not found.");
        }

        return DB::transaction(function () use ($submission, $marks, $feedback): mixed {
            return $this->submissionRepository->update($submission->id, [
                'marks' => $marks,
                'feedback' => $feedback,
                'status' => $marks !== null ? 'graded' : $submission->status,
            ]);
        });
    }
}
