<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'grade_name' => $this->grade_name,
            'grade_letter' => $this->grade_letter,
            'min_mark' => (float) $this->min_mark,
            'max_mark' => (float) $this->max_mark,
            'gpa_point' => (float) $this->gpa_point,
            'remarks' => $this->remarks,
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
