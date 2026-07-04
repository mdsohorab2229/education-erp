<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'student' => $this->whenLoaded('student', fn () => [
                'id' => $this->student->id,
                'admission_no' => $this->student->admission_no,
                'roll_no' => $this->student->roll_no,
                'name' => $this->student->full_name,
                'photo' => $this->student->photo,
            ]),
            'obtained_mark' => $this->obtained_mark !== null ? (float) $this->obtained_mark : null,
            'practical_mark' => $this->practical_mark !== null ? (float) $this->practical_mark : null,
            'viva_mark' => $this->viva_mark !== null ? (float) $this->viva_mark : null,
            'total_mark' => $this->total_mark !== null ? (float) $this->total_mark : null,
            'grade' => $this->whenLoaded('grade', fn () => [
                'id' => $this->grade->id,
                'grade_name' => $this->grade->grade_name,
                'grade_letter' => $this->grade->grade_letter,
                'gpa_point' => (float) $this->grade->gpa_point,
            ]),
            'approval_status' => $this->approval_status,
            'approved_by' => $this->whenLoaded('approvedBy', fn () => $this->approvedBy ? [
                'id' => $this->approvedBy->id,
                'name' => $this->approvedBy->name,
            ] : null),
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'remark' => $this->remark,
        ];
    }
}
