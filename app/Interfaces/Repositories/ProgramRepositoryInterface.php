<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Collection;

interface ProgramRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Program;

    public function create(array $data): Program;

    public function update(int $id, array $data): Program;

    public function delete(int $id): bool;
}
