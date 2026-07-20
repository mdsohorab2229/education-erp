<?php
declare(strict_types=1);

namespace App\Http\Requests\Examination;

use App\Rules\MarkNotExceedFullMark;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\ValidationResponseException;

class MarksEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $examSubjectId = (int) $this->input('exam_subject_id');

        return [
            'exam_subject_id' => ['required', 'exists:exam_subjects,id'],
            'marks' => ['required', 'array', 'min:1'],
            'marks.*.student_id' => ['required', 'exists:students,id'],
            'marks.*.obtained_mark' => [
                'required',
                'numeric',
                'min:0',
                new MarkNotExceedFullMark($examSubjectId),
            ],
            'marks.*.practical_mark' => ['nullable', 'numeric', 'min:0'],
            'marks.*.viva_mark' => ['nullable', 'numeric', 'min:0'],
            'marks.*.remark' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'exam_subject_id.required' => 'The exam subject is required.',
            'exam_subject_id.exists' => 'The selected exam subject is invalid.',
            'marks.required' => 'At least one mark entry is required.',
            'marks.min' => 'At least one mark entry is required.',
            'marks.*.student_id.required' => 'The student is required.',
            'marks.*.student_id.exists' => 'The selected student is invalid.',
            'marks.*.obtained_mark.required' => 'The obtained mark is required.',
            'marks.*.obtained_mark.numeric' => 'The obtained mark must be a number.',
            'marks.*.obtained_mark.min' => 'The obtained mark cannot be negative.',
            'marks.*.practical_mark.numeric' => 'The practical mark must be a number.',
            'marks.*.practical_mark.min' => 'The practical mark cannot be negative.',
            'marks.*.viva_mark.numeric' => 'The viva mark must be a number.',
            'marks.*.viva_mark.min' => 'The viva mark cannot be negative.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            parent::failedValidation($validator);

            return;
        }

        throw new ValidationResponseException(
            $validator,
            redirect()->route('admin.marks.index', ['exam_subject_id' => $this->input('exam_subject_id')])
                ->withErrors($validator)
                ->withInput(),
        );
    }
}
