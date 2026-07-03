<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'program_id' => $this->program_id,
            'name' => $this->name,
            'code' => $this->code,
            'credits' => $this->credits,
            'type' => $this->type,
            'program' => new ProgramResource($this->whenLoaded('program')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
