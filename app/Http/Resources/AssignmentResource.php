<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'attachment' => $this->attachment,
            'due_date' => $this->due_date?->toDateString(),
            'total_marks' => $this->total_marks !== null ? (float) $this->total_marks : null,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'teacher' => $this->whenLoaded('teacher', fn () => [
                'id' => $this->teacher->id,
                'name' => $this->teacher->name,
            ]),
            'subject' => $this->whenLoaded('subject', fn () => [
                'id' => $this->subject->id,
                'name' => $this->subject->name,
            ]),
            'section' => $this->whenLoaded('section', fn () => [
                'id' => $this->section->id,
                'name' => $this->section->name,
            ]),
        ];
    }
}
