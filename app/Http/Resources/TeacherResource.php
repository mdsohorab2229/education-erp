<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'address' => $this->address,
            'designation' => $this->designation,
            'joining_date' => $this->joining_date?->format('Y-m-d'),
            'status' => $this->status,
            'departments' => DepartmentResource::collection($this->whenLoaded('departments')),
            'subjects' => SubjectResource::collection($this->whenLoaded('subjects')),
            'qualifications' => TeacherQualificationResource::collection($this->whenLoaded('qualifications')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
