<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:1000'],
            'designation' => ['nullable', 'string', 'max:100'],
            'joining_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:active,inactive,suspended'],

            'qualifications' => ['nullable', 'array'],
            'qualifications.*.degree' => ['required_with:qualifications', 'string', 'max:150'],
            'qualifications.*.institution' => ['required_with:qualifications', 'string', 'max:200'],
            'qualifications.*.year' => ['nullable', 'integer', 'min:1950', 'max:' . date('Y')],
            'qualifications.*.grade' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.before' => 'Date of birth must be before today.',
        ];
    }
}
