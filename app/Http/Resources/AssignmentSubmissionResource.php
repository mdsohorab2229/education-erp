<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'submission_file' => $this->submission_file,
            'submitted_at' => $this->submitted_at?->toISOString(),
            'marks' => $this->marks !== null ? (float) $this->marks : null,
            'feedback' => $this->feedback,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'assignment' => $this->whenLoaded('assignment', fn () => [
                'id' => $this->assignment->id,
                'title' => $this->assignment->title,
            ]),
            'student' => $this->whenLoaded('student', fn () => [
                'id' => $this->student->id,
                'name' => $this->student->name,
            ]),
        ];
    }
}
