<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'student_id' => $this->student_id,
            'roll_no' => $this->whenLoaded('student', fn () => $this->student->roll_no),
            'student_name' => $this->whenLoaded('student', fn () => $this->student->full_name),
            'student_photo' => $this->whenLoaded('student', fn () => $this->student->photo),
            'attendance_status' => $this->attendance_status,
            'remark' => $this->remark,
            'checked_at' => $this->checked_at?->toISOString(),
        ];
    }
}
