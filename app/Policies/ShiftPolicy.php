<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class ShiftPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('shift-list');
    }

    public function view(User $user): bool
    {
        return $user->can('shift-list');
    }

    public function create(User $user): bool
    {
        return $user->can('shift-create');
    }

    public function update(User $user): bool
    {
        return $user->can('shift-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('shift-delete');
    }
}
