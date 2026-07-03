<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('role-list');
    }

    public function view(User $user): bool
    {
        return $user->can('role-list');
    }

    public function create(User $user): bool
    {
        return $user->can('role-create');
    }

    public function update(User $user): bool
    {
        return $user->can('role-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('role-delete');
    }
}
