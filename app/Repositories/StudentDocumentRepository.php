<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\StudentDocumentRepositoryInterface;
use App\Models\StudentDocument;
use Illuminate\Database\Eloquent\Collection;

class StudentDocumentRepository implements StudentDocumentRepositoryInterface
{
    public function __construct(
        private readonly StudentDocument $model
    ) {}

    public function create(array $data): StudentDocument
    {
        return $this->model->create($data);
    }

    public function findByStudentId(int $studentId): Collection
    {
        return $this->model->where('student_id', $studentId)->get();
    }

    public function delete(int $id): bool
    {
        $document = $this->model->find($id);
        if (!$document) {
            throw new \RuntimeException("Document with ID {$id} not found.");
        }

        return (bool) $document->delete();
    }
}
