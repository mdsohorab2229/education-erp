<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class ProgramPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('program-list');
    }

    public function view(User $user): bool
    {
        return $user->can('program-list');
    }

    public function create(User $user): bool
    {
        return $user->can('program-create');
    }

    public function update(User $user): bool
    {
        return $user->can('program-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('program-delete');
    }
}
