<?php
declare(strict_types=1);

namespace App\Http\Requests\Examination;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exam_type_id' => ['sometimes', 'required', 'exists:exam_types,id'],
            'academic_year_id' => ['sometimes', 'required', 'exists:academic_years,id'],
            'semester_id' => ['sometimes', 'required', 'exists:semesters,id'],
            'department_id' => ['sometimes', 'required', 'exists:departments,id'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'shift_id' => ['sometimes', 'required', 'exists:shifts,id'],
            'section_id' => ['sometimes', 'required', 'exists:sections,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
            'status' => ['sometimes', 'string', Rule::in(['draft', 'published', 'completed'])],
        ];
    }

    public function messages(): array
    {
        return [
            'exam_type_id.exists' => 'The selected exam type is invalid.',
            'academic_year_id.exists' => 'The selected academic year is invalid.',
            'semester_id.exists' => 'The selected semester is invalid.',
            'department_id.exists' => 'The selected department is invalid.',
            'program_id.exists' => 'The selected program is invalid.',
            'shift_id.exists' => 'The selected shift is invalid.',
            'section_id.exists' => 'The selected section is invalid.',
            'title.required' => 'The exam title is required.',
            'start_date.required' => 'The start date is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'end_date.required' => 'The end date is required.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after_or_equal' => 'The end date must be on or after the start date.',
            'status.in' => 'The status must be one of: draft, published, completed.',
        ];
    }
}
