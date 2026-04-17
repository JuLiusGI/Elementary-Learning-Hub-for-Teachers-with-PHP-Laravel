<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLearningMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'quarter' => ['required', 'in:Q1,Q2,Q3,Q4'],
            'week_number' => ['nullable', 'integer', 'min:1', 'max:10'],
            'description' => ['nullable', 'string'],
            'file_type' => ['required', 'in:pdf,image,video,link'],
            'file' => ['nullable', 'file'],
            'external_url' => ['nullable', 'url', 'max:500'],
            'is_downloadable' => ['nullable', 'boolean'],
            'grade_level' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $fileType = $this->input('file_type');

            if (in_array($fileType, ['pdf', 'image']) && !$this->hasFile('file')) {
                $validator->errors()->add('file', 'A file is required for ' . $fileType . ' materials.');
            }

            if (in_array($fileType, ['video', 'link']) && empty($this->input('external_url'))) {
                $validator->errors()->add('external_url', 'A URL is required for ' . $fileType . ' materials.');
            }

            if ($this->hasFile('file')) {
                $file = $this->file('file');
                $maxSize = $fileType === 'pdf' ? 50 * 1024 * 1024 : 10 * 1024 * 1024;
                if ($file->getSize() > $maxSize) {
                    $label = $fileType === 'pdf' ? '50MB' : '10MB';
                    $validator->errors()->add('file', "File size must not exceed {$label}.");
                }

                $allowedMimes = $fileType === 'pdf'
                    ? ['application/pdf']
                    : ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    $validator->errors()->add('file', 'Invalid file type.');
                }
            }
        });
    }
}
