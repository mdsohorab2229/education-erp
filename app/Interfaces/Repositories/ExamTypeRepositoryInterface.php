<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\ExamType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ExamTypeRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): ExamType;
    public function update(int $id, array $data): ExamType;
    public function delete(int $id): bool;
    public function active(): Collection;
}
