<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\StudentDocument;
use Illuminate\Database\Eloquent\Collection;

interface StudentDocumentRepositoryInterface
{
    public function create(array $data): StudentDocument;

    public function findByStudentId(int $studentId): Collection;

    public function delete(int $id): bool;
}
