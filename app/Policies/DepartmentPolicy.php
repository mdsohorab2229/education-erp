<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('department-list');
    }

    public function view(User $user): bool
    {
        return $user->can('department-list');
    }

    public function create(User $user): bool
    {
        return $user->can('department-create');
    }

    public function update(User $user): bool
    {
        return $user->can('department-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('department-delete');
    }
}
