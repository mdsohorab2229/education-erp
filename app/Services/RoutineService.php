<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\RoutineRepositoryInterface;
use App\Models\Routine;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RoutineService
{
    public function __construct(
        private readonly RoutineRepositoryInterface $repository,
        private readonly RoutineConflictDetectionService $conflictDetection,
    ) {}

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function create(array $data): Routine
    {
        $this->conflictDetection->checkTeacherConflict(
            (int) $data['teacher_id'],
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time'],
        );

        $this->conflictDetection->checkRoomConflict(
            (int) $data['room_id'],
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time'],
        );

        $this->conflictDetection->checkSectionTimeOverlap(
            (int) $data['section_id'],
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time'],
        );

        return DB::transaction(function () use ($data): Routine {
            return $this->repository->create($data);
        });
    }

    public function update(int $id, array $data): Routine
    {
        $routine = $this->repository->findById($id);

        if (!$routine) {
            throw new \RuntimeException("Routine with ID {$id} not found.");
        }

        $teacherId = (int) ($data['teacher_id'] ?? $routine->teacher_id);
        $roomId = (int) ($data['room_id'] ?? $routine->room_id);
        $sectionId = (int) ($data['section_id'] ?? $routine->section_id);
        $dayOfWeek = $data['day_of_week'] ?? $routine->day_of_week;
        $startTime = $data['start_time'] ?? $routine->start_time;
        $endTime = $data['end_time'] ?? $routine->end_time;

        $this->conflictDetection->checkTeacherConflict($teacherId, $dayOfWeek, $startTime, $endTime, $id);
        $this->conflictDetection->checkRoomConflict($roomId, $dayOfWeek, $startTime, $endTime, $id);
        $this->conflictDetection->checkSectionTimeOverlap($sectionId, $dayOfWeek, $startTime, $endTime, $id);

        return DB::transaction(function () use ($id, $data): Routine {
            return $this->repository->update($id, $data);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->repository->delete($id);
        });
    }

    public function findById(int $id): ?Routine
    {
        return $this->repository->findById($id);
    }

    public function getWeeklyRoutine(int $sectionId, ?string $dayOfWeek = null): Collection
    {
        return $this->repository->getWeeklyRoutine($sectionId, $dayOfWeek);
    }

    public function getTeacherRoutine(int $teacherId, ?string $dayOfWeek = null): Collection
    {
        return $this->repository->getTeacherRoutine($teacherId, $dayOfWeek);
    }

    public function getStudentRoutine(int $sectionId, ?int $groupId = null, ?string $dayOfWeek = null): Collection
    {
        return $this->repository->getBySectionAndGroup($sectionId, $groupId, $dayOfWeek);
    }
}
