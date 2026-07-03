<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class SubjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('subject-list');
    }

    public function view(User $user): bool
    {
        return $user->can('subject-list');
    }

    public function create(User $user): bool
    {
        return $user->can('subject-create');
    }

    public function update(User $user): bool
    {
        return $user->can('subject-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('subject-delete');
    }
}
