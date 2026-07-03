<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;

interface SubjectRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Subject;

    public function create(array $data): Subject;

    public function update(int $id, array $data): Subject;

    public function delete(int $id): bool;
}
