<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'teacher_id' => ['sometimes', 'exists:users,id'],
            'subject_id' => ['sometimes', 'exists:subjects,id'],
            'section_id' => ['sometimes', 'exists:sections,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:20', Rule::in(['pdf', 'video', 'notes'])],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'max:20', Rule::in(['active', 'inactive'])],
        ];

        $type = $this->input('type');

        if ($type) {
            $typeMimeMap = [
                'pdf' => 'application/pdf',
                'video' => 'video/mp4,video/avi,video/mkv,video/mov,video/wmv,video/webm',
                'notes' => 'application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain,application/pdf',
            ];

            $typeExtMap = [
                'pdf' => 'pdf',
                'video' => 'mp4,avi,mkv,mov,wmv,webm',
                'notes' => 'doc,docx,txt,pdf',
            ];

            $typeMaxSize = [
                'pdf' => 51200,
                'video' => 204800,
                'notes' => 10240,
            ];

            if (isset($typeMimeMap[$type])) {
                $rules['file'] = [
                    'sometimes',
                    'file',
                    'mimetypes:' . $typeMimeMap[$type],
                    'mimes:' . $typeExtMap[$type],
                    'max:' . $typeMaxSize[$type],
                ];
            } else {
                $rules['file'] = ['sometimes', 'file', 'max:51200'];
            }
        } else {
            $rules['file'] = ['sometimes', 'file', 'max:51200'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'file.mimetypes' => 'The file type does not match the selected content type.',
            'file.mimes' => 'The file must be a valid type for the selected content category.',
            'file.max' => 'The file size exceeds the maximum allowed size for this content type.',
            'type.in' => 'Content type must be one of: pdf, video, notes.',
        ];
    }
}
