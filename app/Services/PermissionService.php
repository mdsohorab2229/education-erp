<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function __construct(
        private readonly PermissionRepositoryInterface $permissionRepository
    ) {}

    public function all(): Collection
    {
        return $this->permissionRepository->all();
    }

    public function findById(int $id): ?Permission
    {
        return $this->permissionRepository->findById($id);
    }

    public function create(array $data): Permission
    {
        return DB::transaction(fn(): Permission => $this->permissionRepository->create($data));
    }

    public function update(int $id, array $data): Permission
    {
        return DB::transaction(fn(): Permission => $this->permissionRepository->update($id, $data));
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->permissionRepository->delete($id);
        });
    }
}
