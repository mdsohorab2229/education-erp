<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\AssignmentRepositoryInterface;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
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
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function findById(int $id): ?Assignment
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Assignment
    {
        return DB::transaction(function () use ($data): Assignment {
            return $this->repository->create($data);
        });
    }

    public function update(int $id, array $data): Assignment
    {
        return DB::transaction(function () use ($id, $data): Assignment {
            return $this->repository->update($id, $data);
        });
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

    public function submit(int $assignmentId, int $studentId, UploadedFile $file): AssignmentSubmission
    {
        $assignment = $this->repository->findById($assignmentId);

        if (!$assignment) {
            throw new \RuntimeException("Assignment with ID {$assignmentId} not found.");
        }

        $student = Student::find($studentId);

        if (!$student) {
            throw new \RuntimeException("Student with ID {$studentId} not found.");
        }

        if (AssignmentSubmission::where('assignment_id', $assignmentId)->where('student_id', $studentId)->exists()) {
            throw ValidationException::withMessages([
                'assignment_id' => ['A submission already exists for this assignment and student.'],
            ]);
        }

        return DB::transaction(function () use ($assignment, $studentId, $file): AssignmentSubmission {
            $submissionFile = $file->store(self::SUBMISSION_PATH, self::DISK);

            return $assignment->submissions()->create([
                'assignment_id' => $assignment->id,
                'student_id' => $studentId,
                'submission_file' => $submissionFile,
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);
        });
    }

    public function updateMarks(int $submissionId, ?float $marks, ?string $feedback = null): AssignmentSubmission
    {
        $submission = AssignmentSubmission::find($submissionId);

        if (!$submission) {
            throw new \RuntimeException("Submission with ID {$submissionId} not found.");
        }

        return DB::transaction(function () use ($submission, $marks, $feedback): AssignmentSubmission {
            $submission->update([
                'marks' => $marks,
                'feedback' => $feedback,
                'status' => $marks !== null ? 'graded' : $submission->status,
            ]);

            return $submission;
        });
    }
}
