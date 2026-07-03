<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;

interface PermissionRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Permission;

    public function create(array $data): Permission;

    public function update(int $id, array $data): Permission;

    public function delete(int $id): bool;
}
