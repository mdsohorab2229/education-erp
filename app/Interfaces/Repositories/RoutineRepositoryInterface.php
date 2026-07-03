<?php
declare(strict_types=1);

namespace App\Interfaces\Repositories;

use App\Models\Routine;
use Illuminate\Database\Eloquent\Collection;

interface RoutineRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Routine;

    public function create(array $data): Routine;

    public function update(int $id, array $data): Routine;

    public function delete(int $id): bool;

    public function getWeeklyRoutine(int $sectionId, ?string $dayOfWeek = null): Collection;

    public function getTeacherRoutine(int $teacherId, ?string $dayOfWeek = null): Collection;

    public function getByRoom(int $roomId, ?string $dayOfWeek = null): Collection;

    public function getBySectionAndGroup(int $sectionId, ?int $groupId = null, ?string $dayOfWeek = null): Collection;
}
