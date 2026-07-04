<?php
declare(strict_types=1);

namespace App\Http\Requests\Examination;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExamTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('exam_type');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('exam_types', 'name')->ignore($id)],
            'code' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('exam_types', 'code')->ignore($id)],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The exam type name is required.',
            'name.unique' => 'This exam type name already exists.',
            'code.required' => 'The exam type code is required.',
            'code.unique' => 'This exam type code already exists.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }
}
