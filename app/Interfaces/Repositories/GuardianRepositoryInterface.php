<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Guardian;

interface GuardianRepositoryInterface
{
    public function create(array $data): Guardian;

    public function updateByStudentId(int $studentId, array $data): Guardian;

    public function findByStudentId(int $studentId): ?Guardian;
}
