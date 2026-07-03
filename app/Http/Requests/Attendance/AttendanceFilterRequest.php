<?php
declare(strict_types=1);

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'shift_id' => ['required', 'integer', 'exists:shifts,id'],
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'attendance_date' => ['required', 'date', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'attendance_date.date_format' => 'The attendance date must be in YYYY-MM-DD format.',
        ];
    }
}
