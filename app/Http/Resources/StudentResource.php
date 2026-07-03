<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'admission_no' => $this->admission_no,
            'roll_no' => $this->roll_no,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'photo' => $this->photo,
            'blood_group' => $this->blood_group,
            'status' => $this->status,
            'academic_year' => new AcademicYearResource($this->whenLoaded('academicYear')),
            'program' => new ProgramResource($this->whenLoaded('program')),
            'section' => new SectionResource($this->whenLoaded('section')),
            'shift' => new ShiftResource($this->whenLoaded('shift')),
            'group' => new GroupResource($this->whenLoaded('group')),
            'guardian' => new GuardianResource($this->whenLoaded('guardian')),
            'documents' => StudentDocumentResource::collection($this->whenLoaded('documents')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
