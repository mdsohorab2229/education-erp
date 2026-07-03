<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('student-list');
    }

    public function view(User $user): bool
    {
        return $user->can('student-list');
    }

    public function create(User $user): bool
    {
        return $user->can('student-create');
    }

    public function update(User $user): bool
    {
        return $user->can('student-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('student-delete');
    }
}
