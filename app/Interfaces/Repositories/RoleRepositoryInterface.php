<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

interface RoleRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Role;

    public function create(array $data): Role;

    public function update(int $id, array $data): Role;

    public function delete(int $id): bool;

    public function givePermissions(int $roleId, array $permissions): Role;

    public function syncPermissions(int $roleId, array $permissions): Role;
}
