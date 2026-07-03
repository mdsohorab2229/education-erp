<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class AcademicYearPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('academic-year-list');
    }

    public function view(User $user): bool
    {
        return $user->can('academic-year-list');
    }

    public function create(User $user): bool
    {
        return $user->can('academic-year-create');
    }

    public function update(User $user): bool
    {
        return $user->can('academic-year-edit');
    }

    public function delete(User $user): bool
    {
        return $user->can('academic-year-delete');
    }
}
