<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\RoutineRepositoryInterface;
use App\Models\Routine;
use Illuminate\Database\Eloquent\Collection;

class RoutineRepository implements RoutineRepositoryInterface
{
    private const array EAGER_LOADS = [
        'academicYear',
        'semester',
        'department',
        'program',
        'shift',
        'group',
        'section',
        'subject',
        'teacher',
        'room',
    ];

    public function __construct(
        private readonly Routine $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with(self::EAGER_LOADS)->get();
    }

    public function findById(int $id): ?Routine
    {
        return $this->model->with(self::EAGER_LOADS)->find($id);
    }

    public function create(array $data): Routine
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Routine
    {
        $routine = $this->model->find($id);
        if (!$routine) {
            throw new \RuntimeException("Routine with ID {$id} not found.");
        }
        $routine->update($data);

        return $routine;
    }

    public function delete(int $id): bool
    {
        $routine = $this->model->find($id);
        if (!$routine) {
            throw new \RuntimeException("Routine with ID {$id} not found.");
        }

        return (bool) $routine->delete();
    }

    public function getWeeklyRoutine(int $sectionId, ?string $dayOfWeek = null): Collection
    {
        $query = $this->model->with(self::EAGER_LOADS)
            ->where('section_id', $sectionId);

        if ($dayOfWeek) {
            $query->where('day_of_week', $dayOfWeek);
        }

        return $query->orderBy('day_of_week')->orderBy('start_time')->get();
    }

    public function getTeacherRoutine(int $teacherId, ?string $dayOfWeek = null): Collection
    {
        $query = $this->model->with(self::EAGER_LOADS)
            ->where('teacher_id', $teacherId);

        if ($dayOfWeek) {
            $query->where('day_of_week', $dayOfWeek);
        }

        return $query->orderBy('day_of_week')->orderBy('start_time')->get();
    }

    public function getByRoom(int $roomId, ?string $dayOfWeek = null): Collection
    {
        $query = $this->model->with(self::EAGER_LOADS)
            ->where('room_id', $roomId);

        if ($dayOfWeek) {
            $query->where('day_of_week', $dayOfWeek);
        }

        return $query->orderBy('day_of_week')->orderBy('start_time')->get();
    }

    public function getBySectionAndGroup(int $sectionId, ?int $groupId = null, ?string $dayOfWeek = null): Collection
    {
        $query = $this->model->with(self::EAGER_LOADS)
            ->where('section_id', $sectionId);

        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        if ($dayOfWeek) {
            $query->where('day_of_week', $dayOfWeek);
        }

        return $query->orderBy('day_of_week')->orderBy('start_time')->get();
    }
}
