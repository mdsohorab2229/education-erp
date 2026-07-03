<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attendance_date' => $this->attendance_date?->format('Y-m-d'),
            'academic_year' => new AcademicYearResource($this->whenLoaded('academicYear')),
            'semester' => $this->whenLoaded('semester', fn () => [
                'id' => $this->semester->id,
                'name' => $this->semester->name,
            ]),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'program' => new ProgramResource($this->whenLoaded('program')),
            'shift' => new ShiftResource($this->whenLoaded('shift')),
            'group' => new GroupResource($this->whenLoaded('group')),
            'section' => new SectionResource($this->whenLoaded('section')),
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'teacher' => $this->whenLoaded('teacher', fn () => [
                'id' => $this->teacher->id,
                'name' => $this->teacher->name,
            ]),
            'summary' => [
                'total_students' => $this->total_students,
                'present_count' => $this->present_count,
                'absent_count' => $this->absent_count,
                'late_count' => $this->late_count,
                'leave_count' => $this->leave_count,
            ],
            'status' => $this->status,
        ];
    }
}
