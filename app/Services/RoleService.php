<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository
    ) {}

    public function all(): Collection
    {
        return $this->roleRepository->all();
    }

    public function findById(int $id): ?Role
    {
        return $this->roleRepository->findById($id);
    }

    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data): Role {
            $role = $this->roleRepository->create($data);
            if (!empty($data['permissions'])) {
                $this->roleRepository->syncPermissions($role->id, $data['permissions']);
            }

            return $role;
        });
    }

    public function update(int $id, array $data): Role
    {
        return DB::transaction(function () use ($id, $data): Role {
            $role = $this->roleRepository->update($id, $data);
            if (isset($data['permissions'])) {
                $this->roleRepository->syncPermissions($role->id, $data['permissions']);
            }

            return $role;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->roleRepository->delete($id);
        });
    }
}
