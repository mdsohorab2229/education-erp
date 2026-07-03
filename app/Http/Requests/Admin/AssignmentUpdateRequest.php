<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => ['sometimes', 'exists:users,id'],
            'subject_id' => ['sometimes', 'exists:subjects,id'],
            'section_id' => ['sometimes', 'exists:sections,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:51200'],
            'due_date' => ['sometimes', 'date', 'after:today'],
            'total_marks' => ['sometimes', 'numeric', 'min:0', 'max:999.99'],
            'status' => ['sometimes', 'string', 'max:20', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'due_date.after' => 'The due date must be a future date.',
            'total_marks.max' => 'Total marks must not exceed 999.99.',
        ];
    }
}
