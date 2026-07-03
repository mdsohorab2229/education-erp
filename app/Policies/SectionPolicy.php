<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class SectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('section-list');
    }

    public function view(User $user): bool
    {
        return $user->can('section-list');
    }

    public function create(User $user): bool
    {
        return $user->can('section-create');
    }

    public function update(User $user): bool
    {
        return $user->can('section-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('section-delete');
    }
}
