<?php
declare(strict_types=1);

namespace App\Http\Requests\Examination;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exam_id' => ['sometimes', 'required', 'exists:exams,id'],
            'subject_id' => ['sometimes', 'required', 'exists:subjects,id'],
            'teacher_id' => ['sometimes', 'required', 'exists:users,id'],
            'full_mark' => ['sometimes', 'required', 'numeric', 'gt:0'],
            'pass_mark' => ['sometimes', 'required', 'numeric', 'min:0', 'lte:full_mark'],
            'practical_mark' => ['nullable', 'numeric', 'min:0', 'lte:full_mark'],
            'viva_mark' => ['nullable', 'numeric', 'min:0', 'lte:full_mark'],
        ];
    }

    public function messages(): array
    {
        return [
            'exam_id.exists' => 'The selected exam is invalid.',
            'subject_id.exists' => 'The selected subject is invalid.',
            'teacher_id.exists' => 'The selected teacher is invalid.',
            'full_mark.required' => 'The full mark is required.',
            'full_mark.numeric' => 'The full mark must be a number.',
            'full_mark.gt' => 'The full mark must be greater than 0.',
            'pass_mark.required' => 'The pass mark is required.',
            'pass_mark.numeric' => 'The pass mark must be a number.',
            'pass_mark.lte' => 'The pass mark must not exceed the full mark.',
            'practical_mark.numeric' => 'The practical mark must be a number.',
            'practical_mark.lte' => 'The practical mark must not exceed the full mark.',
            'viva_mark.numeric' => 'The viva mark must be a number.',
            'viva_mark.lte' => 'The viva mark must not exceed the full mark.',
        ];
    }
}
