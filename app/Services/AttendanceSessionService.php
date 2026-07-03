<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Repositories\AttendanceSessionRepositoryInterface;
use App\Interfaces\Repositories\AttendanceRecordRepositoryInterface;
use App\Interfaces\Repositories\SectionRepositoryInterface;
use App\Interfaces\Repositories\StudentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AttendanceSessionService
{
    public function __construct(
        private readonly AttendanceSessionRepositoryInterface $sessionRepository,
        private readonly AttendanceRecordRepositoryInterface $recordRepository,
        private readonly SectionRepositoryInterface $sectionRepository,
        private readonly StudentRepositoryInterface $studentRepository,
    ) {}

    public function loadStudents(array $filters, int $teacherId): array
    {
        $section = $this->sectionRepository->findById((int) $filters['section_id']);

        if (!$section) {
            throw new \RuntimeException('Section not found.');
        }

        $sessionFilters = [
            'academic_year_id' => (int) $filters['academic_year_id'],
            'semester_id' => (int) $filters['semester_id'],
            'department_id' => (int) $filters['department_id'],
            'program_id' => $section->program_id,
            'shift_id' => (int) $filters['shift_id'],
            'section_id' => (int) $filters['section_id'],
            'subject_id' => (int) $filters['subject_id'],
            'teacher_id' => $teacherId,
            'attendance_date' => $filters['attendance_date'],
        ];

        if (!empty($filters['group_id'])) {
            $sessionFilters['group_id'] = (int) $filters['group_id'];
        }

        $sessionData = array_merge($sessionFilters, [
            'status' => 'active',
            'created_by' => $teacherId,
            'updated_by' => $teacherId,
        ]);

        $session = $this->sessionRepository->findOrCreate($sessionFilters, $sessionData);

        $students = $this->studentRepository->paginateWithFilters([
            'academic_year_id' => (int) $filters['academic_year_id'],
            'program_id' => $section->program_id,
            'section_id' => (int) $filters['section_id'],
            'shift_id' => (int) $filters['shift_id'],
            'status' => 'active',
        ], 500);

        $studentList = collect($students->items());

        if (!empty($filters['group_id'])) {
            $studentList = $studentList->where('group_id', (int) $filters['group_id'])->values();
        }

        $existingCount = $this->recordRepository->countBySession($session->id);

        if ($existingCount === 0) {
            $records = $studentList->map(fn ($student) => [
                'attendance_session_id' => $session->id,
                'student_id' => $student->id,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            $this->recordRepository->bulkCreate($records);

            $this->sessionRepository->updateSummary($session->id, [
                'total_students' => $studentList->count(),
            ]);

            $session = $this->sessionRepository->findByIdWithRelations($session->id);
        }

        $attendanceRecords = $this->recordRepository->getBySessionWithStudent($session->id);

        return [
            'session' => $session,
            'records' => $attendanceRecords,
            'summary' => [
                'total' => $session->total_students,
                'present' => $session->present_count,
                'absent' => $session->absent_count,
                'late' => $session->late_count,
                'leave' => $session->leave_count,
            ],
        ];
    }

    public function findByIdWithRelations(int $id): ?\App\Models\AttendanceSession
    {
        return $this->sessionRepository->findByIdWithRelations($id);
    }

    public function paginateHistory(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->sessionRepository->paginateHistory($filters, $perPage);
    }
}
