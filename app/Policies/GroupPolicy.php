<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('group-list');
    }

    public function view(User $user): bool
    {
        return $user->can('group-list');
    }

    public function create(User $user): bool
    {
        return $user->can('group-create');
    }

    public function update(User $user): bool
    {
        return $user->can('group-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('group-delete');
    }
}
