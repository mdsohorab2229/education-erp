<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Collection;

interface ShiftRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Shift;

    public function create(array $data): Shift;

    public function update(int $id, array $data): Shift;

    public function delete(int $id): bool;
}
