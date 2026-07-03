<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoutineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => ['sometimes', 'required', 'exists:academic_years,id'],
            'semester_id' => ['sometimes', 'required', 'exists:semesters,id'],
            'department_id' => ['sometimes', 'required', 'exists:departments,id'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'shift_id' => ['sometimes', 'required', 'exists:shifts,id'],
            'group_id' => ['nullable', 'exists:groups,id'],
            'section_id' => ['sometimes', 'required', 'exists:sections,id'],
            'subject_id' => ['sometimes', 'required', 'exists:subjects,id'],
            'teacher_id' => ['sometimes', 'required', 'exists:users,id'],
            'room_id' => ['sometimes', 'required', 'exists:rooms,id'],
            'day_of_week' => ['sometimes', 'required', 'string', 'max:10', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])],
            'start_time' => ['sometimes', 'required', 'date_format:H:i'],
            'end_time' => ['sometimes', 'required', 'date_format:H:i', 'after:start_time'],
            'status' => ['sometimes', 'string', 'max:20', Rule::in(['active', 'inactive'])],
        ];
    }
}
