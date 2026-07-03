<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

interface GroupRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Group;

    public function create(array $data): Group;

    public function update(int $id, array $data): Group;

    public function delete(int $id): bool;
}
