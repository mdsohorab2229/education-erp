<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function __construct(
        private readonly Permission $model
    ) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Permission
    {
        return $this->model->find($id);
    }

    public function create(array $data): Permission
    {
        return $this->model->create(['name' => $data['name']]);
    }

    public function update(int $id, array $data): Permission
    {
        $permission = $this->findById($id);
        if (!$permission) {
            throw new \RuntimeException("Permission with ID {$id} not found.");
        }
        $permission->update(['name' => $data['name']]);

        return $permission;
    }

    public function delete(int $id): bool
    {
        $permission = $this->findById($id);
        if (!$permission) {
            throw new \RuntimeException("Permission with ID {$id} not found.");
        }

        return (bool) $permission->delete();
    }
}
