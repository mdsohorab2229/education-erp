<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\GuardianRepositoryInterface;
use App\Models\Guardian;

class GuardianRepository implements GuardianRepositoryInterface
{
    public function __construct(
        private readonly Guardian $model
    ) {}

    public function create(array $data): Guardian
    {
        return $this->model->create($data);
    }

    public function updateByStudentId(int $studentId, array $data): Guardian
    {
        $guardian = $this->findByStudentId($studentId);
        if (!$guardian) {
            throw new \RuntimeException("Guardian for student ID {$studentId} not found.");
        }
        $guardian->update($data);

        return $guardian;
    }

    public function findByStudentId(int $studentId): ?Guardian
    {
        return $this->model->where('student_id', $studentId)->first();
    }
}
