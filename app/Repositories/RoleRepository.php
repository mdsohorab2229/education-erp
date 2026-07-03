<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function __construct(
        private readonly Role $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with('permissions')->get();
    }

    public function findById(int $id): ?Role
    {
        return $this->model->with('permissions')->find($id);
    }

    public function create(array $data): Role
    {
        return $this->model->create(['name' => $data['name']]);
    }

    public function update(int $id, array $data): Role
    {
        $role = $this->findById($id);
        if (!$role) {
            throw new \RuntimeException("Role with ID {$id} not found.");
        }
        $role->update(['name' => $data['name']]);

        return $role;
    }

    public function delete(int $id): bool
    {
        $role = $this->findById($id);
        if (!$role) {
            throw new \RuntimeException("Role with ID {$id} not found.");
        }

        return (bool) $role->delete();
    }

    public function givePermissions(int $roleId, array $permissions): Role
    {
        $role = $this->findById($roleId);
        if (!$role) {
            throw new \RuntimeException("Role with ID {$roleId} not found.");
        }
        $role->givePermissionTo($permissions);

        return $role;
    }

    public function syncPermissions(int $roleId, array $permissions): Role
    {
        $role = $this->findById($roleId);
        if (!$role) {
            throw new \RuntimeException("Role with ID {$roleId} not found.");
        }
        $role->syncPermissions($permissions);

        return $role;
    }
}
