<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\RoutineConflictException;
use App\Interfaces\Repositories\RoutineRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RoutineConflictDetectionService
{
    public function __construct(
        private readonly RoutineRepositoryInterface $repository
    ) {}

    public function checkTeacherConflict(
        int $teacherId,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int $excludeRoutineId = null
    ): void {
        $routines = $this->repository->getTeacherRoutine($teacherId, $dayOfWeek);
        $conflict = $this->findTimeOverlap($routines, $startTime, $endTime, $excludeRoutineId);

        if ($conflict) {
            throw new RoutineConflictException(
                "Teacher is already booked on {$dayOfWeek} from {$conflict->start_time} to {$conflict->end_time}."
            );
        }
    }

    public function checkRoomConflict(
        int $roomId,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int $excludeRoutineId = null
    ): void {
        $routines = $this->repository->getByRoom($roomId, $dayOfWeek);
        $conflict = $this->findTimeOverlap($routines, $startTime, $endTime, $excludeRoutineId);

        if ($conflict) {
            throw new RoutineConflictException(
                "Room is already booked on {$dayOfWeek} from {$conflict->start_time} to {$conflict->end_time}."
            );
        }
    }

    public function checkSectionTimeOverlap(
        int $sectionId,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int $excludeRoutineId = null
    ): void {
        $routines = $this->repository->getWeeklyRoutine($sectionId, $dayOfWeek);
        $conflict = $this->findTimeOverlap($routines, $startTime, $endTime, $excludeRoutineId);

        if ($conflict) {
            throw new RoutineConflictException(
                "Section already has a class scheduled on {$dayOfWeek} from {$conflict->start_time} to {$conflict->end_time}."
            );
        }
    }

    private function findTimeOverlap(
        Collection $routines,
        string $startTime,
        string $endTime,
        ?int $excludeRoutineId = null
    ): mixed {
        $start = $this->normalizeTime($startTime);
        $end = $this->normalizeTime($endTime);

        return $routines->first(function ($routine) use ($start, $end, $excludeRoutineId): bool {
            if ($excludeRoutineId && $routine->id === $excludeRoutineId) {
                return false;
            }

            $existingStart = $this->normalizeTime($routine->start_time);
            $existingEnd = $this->normalizeTime($routine->end_time);

            return $start < $existingEnd && $end > $existingStart;
        });
    }

    private function normalizeTime(mixed $time): string
    {
        if ($time instanceof \Carbon\Carbon) {
            return $time->format('H:i:s');
        }

        if (is_string($time) && strlen($time) <= 5) {
            return $time . ':00';
        }

        return (string) $time;
    }
}
