<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student');

        return [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'roll_no' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('students', 'roll_no')
                    ->where('shift_id', $this->input('shift_id'))
                    ->ignore($studentId),
            ],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'shift_id' => ['required', 'exists:shifts,id'],
            'group_id' => ['nullable', 'exists:groups,id'],

            'guardian.name' => ['required', 'string', 'max:100'],
            'guardian.relation' => ['required', 'string', 'in:father,mother,guardian'],
            'guardian.phone' => ['required', 'string', 'max:20'],
            'guardian.email' => ['nullable', 'email', 'max:100'],
            'guardian.occupation' => ['nullable', 'string', 'max:100'],
            'guardian.address' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
