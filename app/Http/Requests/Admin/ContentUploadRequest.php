<?php
declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContentUploadRequest extends FormRequest
{
    private const array TYPE_MIME_MAP = [
        'pdf' => 'application/pdf',
        'video' => 'video/mp4,video/avi,video/mkv,video/mov,video/wmv,video/webm',
        'notes' => 'application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain,application/pdf',
    ];

    private const array TYPE_EXT_MAP = [
        'pdf' => 'pdf',
        'video' => 'mp4,avi,mkv,mov,wmv,webm',
        'notes' => 'doc,docx,txt,pdf',
    ];

    private const array TYPE_MAX_SIZE = [
        'pdf' => 51200,
        'video' => 204800,
        'notes' => 10240,
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = $this->input('type');

        $rules = [
            'teacher_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:20', Rule::in(['pdf', 'video', 'notes'])],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'max:20', Rule::in(['active', 'inactive'])],
        ];

        if ($type && isset(self::TYPE_MIME_MAP[$type])) {
            $rules['file'] = [
                'required',
                'file',
                'mimetypes:' . self::TYPE_MIME_MAP[$type],
                'mimes:' . self::TYPE_EXT_MAP[$type],
                'max:' . self::TYPE_MAX_SIZE[$type],
            ];
        } else {
            $rules['file'] = ['required', 'file', 'max:51200'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please upload a file.',
            'file.mimetypes' => 'The file type does not match the selected content type.',
            'file.mimes' => 'The file must be a valid type for the selected content category.',
            'file.max' => 'The file size exceeds the maximum allowed size for this content type.',
            'type.in' => 'Content type must be one of: pdf, video, notes.',
        ];
    }
}
