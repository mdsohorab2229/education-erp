<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignmentSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assignment_id' => ['required', 'exists:assignments,id'],
            'student_id' => ['sometimes', 'required', 'exists:students,id'],
            'submission_file' => ['required', 'file', 'max:51200'],
            'status' => ['sometimes', 'string', 'max:20', Rule::in(['submitted', 'draft'])],
        ];
    }

    public function messages(): array
    {
        return [
            'submission_file.required' => 'Please upload your assignment file.',
            'submission_file.max' => 'The submission file must not exceed 50 MB.',
        ];
    }
}
