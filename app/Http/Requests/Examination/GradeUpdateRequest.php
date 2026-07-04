<?php
declare(strict_types=1);

namespace App\Http\Requests\Examination;

use App\Rules\NoGradeRangeOverlap;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GradeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('grade');

        return [
            'grade_name' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('grades', 'grade_name')->ignore($id)],
            'grade_letter' => ['sometimes', 'required', 'string', 'max:10'],
            'min_mark' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                new NoGradeRangeOverlap(
                    minMark: (float) ($this->input('min_mark') ?? 0),
                    maxMark: (float) ($this->input('max_mark') ?? 0),
                    excludeId: $id,
                ),
            ],
            'max_mark' => ['sometimes', 'required', 'numeric', 'gte:min_mark'],
            'gpa_point' => ['sometimes', 'required', 'numeric', 'min:0', 'max:5'],
            'remarks' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'grade_name.required' => 'The grade name is required.',
            'grade_name.unique' => 'This grade name already exists.',
            'grade_letter.required' => 'The grade letter is required.',
            'min_mark.required' => 'The minimum mark is required.',
            'min_mark.numeric' => 'The minimum mark must be a number.',
            'min_mark.min' => 'The minimum mark cannot be negative.',
            'max_mark.required' => 'The maximum mark is required.',
            'max_mark.numeric' => 'The maximum mark must be a number.',
            'max_mark.gte' => 'The maximum mark must be greater than or equal to the minimum mark.',
            'gpa_point.required' => 'The GPA point is required.',
            'gpa_point.numeric' => 'The GPA point must be a number.',
            'gpa_point.min' => 'The GPA point must be at least 0.',
            'gpa_point.max' => 'The GPA point must not exceed 5.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }
}
