<?php
declare(strict_types=1);

namespace App\Http\Requests\Examination;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'obtained_mark' => ['sometimes', 'required', 'numeric', 'min:0'],
            'practical_mark' => ['nullable', 'numeric', 'min:0'],
            'viva_mark' => ['nullable', 'numeric', 'min:0'],
            'remark' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'obtained_mark.required' => 'The obtained mark is required.',
            'obtained_mark.numeric' => 'The obtained mark must be a number.',
            'obtained_mark.min' => 'The obtained mark cannot be negative.',
            'practical_mark.numeric' => 'The practical mark must be a number.',
            'practical_mark.min' => 'The practical mark cannot be negative.',
            'viva_mark.numeric' => 'The viva mark must be a number.',
            'viva_mark.min' => 'The viva mark cannot be negative.',
        ];
    }
}
