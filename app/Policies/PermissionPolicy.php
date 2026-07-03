<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('permission-list');
    }

    public function view(User $user): bool
    {
        return $user->can('permission-list');
    }

    public function create(User $user): bool
    {
        return $user->can('permission-create');
    }

    public function update(User $user): bool
    {
        return $user->can('permission-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('permission-delete');
    }
}
