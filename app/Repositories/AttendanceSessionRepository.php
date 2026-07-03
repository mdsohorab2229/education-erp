<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\AttendanceSessionRepositoryInterface;
use App\Models\AttendanceSession;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AttendanceSessionRepository implements AttendanceSessionRepositoryInterface
{
    public function __construct(
        private readonly AttendanceSession $model
    ) {}

    public function findById(int $id): ?AttendanceSession
    {
        return $this->model->find($id);
    }

    public function findByIdWithRelations(int $id): ?AttendanceSession
    {
        return $this->model->with([
            'academicYear',
            'semester',
            'department',
            'program',
            'shift',
            'group',
            'section',
            'subject',
            'teacher',
        ])->find($id);
    }

    public function findSessionByFilters(array $filters): ?AttendanceSession
    {
        return $this->applyFilterCriteria(
            $this->model->newQuery(),
            $filters
        )->first();
    }

    public function create(array $data): AttendanceSession
    {
        return $this->model->create($data);
    }

    public function findOrCreate(array $filters, array $data): AttendanceSession
    {
        $session = $this->findSessionByFilters($filters);

        if ($session) {
            return $session;
        }

        return $this->create($data);
    }

    public function updateSummary(int $sessionId, array $summary): AttendanceSession
    {
        $session = $this->findById($sessionId);
        if (!$session) {
            throw new \RuntimeException("Attendance session with ID {$sessionId} not found.");
        }
        $session->update($summary);

        return $session;
    }

    public function updateStatus(int $sessionId, string $status): AttendanceSession
    {
        $session = $this->findById($sessionId);
        if (!$session) {
            throw new \RuntimeException("Attendance session with ID {$sessionId} not found.");
        }
        $session->update(['status' => $status]);

        return $session;
    }

    public function exists(array $filters): bool
    {
        return $this->applyFilterCriteria(
            $this->model->newQuery(),
            $filters
        )->exists();
    }

    public function paginateHistory(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with([
            'academicYear',
            'semester',
            'department',
            'section',
            'subject',
            'teacher',
        ]);

        $query = $this->applyFilterCriteria($query, $filters);

        return $query->orderBy('attendance_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    private function applyFilterCriteria(Builder $query, array $filters): Builder
    {
        $filterable = [
            'academic_year_id',
            'semester_id',
            'department_id',
            'program_id',
            'shift_id',
            'group_id',
            'section_id',
            'subject_id',
            'teacher_id',
            'status',
        ];

        foreach ($filterable as $field) {
            if (isset($filters[$field]) && $filters[$field] !== '' && $filters[$field] !== null) {
                $query->where($field, $filters[$field]);
            }
        }

        if (!empty($filters['attendance_date'])) {
            $query->whereDate('attendance_date', $filters['attendance_date']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('attendance_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('attendance_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->whereHas('teacher', fn ($t) => $t->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('section', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('subject', fn ($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        return $query;
    }
}
