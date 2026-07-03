<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'program_id' => ['required', 'exists:programs,id'],
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sections', 'name')->where('program_id', $this->input('program_id')),
            ],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:99999'],
        ];
    }
}
