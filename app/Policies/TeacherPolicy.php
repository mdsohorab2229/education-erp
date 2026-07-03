<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class TeacherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('teacher-list');
    }

    public function view(User $user): bool
    {
        return $user->can('teacher-list');
    }

    public function create(User $user): bool
    {
        return $user->can('teacher-create');
    }

    public function update(User $user): bool
    {
        return $user->can('teacher-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('teacher-delete');
    }
}
