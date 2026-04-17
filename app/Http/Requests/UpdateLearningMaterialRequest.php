<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLearningMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'subject_id' => ['sometimes', 'required', 'exists:subjects,id'],
            'quarter' => ['sometimes', 'required', 'in:Q1,Q2,Q3,Q4'],
            'week_number' => ['nullable', 'integer', 'min:1', 'max:10'],
            'description' => ['nullable', 'string'],
            'file_type' => ['sometimes', 'required', 'in:pdf,image,video,link'],
            'file' => ['nullable', 'file'],
            'external_url' => ['nullable', 'url', 'max:500'],
            'is_downloadable' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasFile('file')) {
                $file = $this->file('file');
                $fileType = $this->input('file_type', $this->route('learning_material')->file_type);
                $maxSize = $fileType === 'pdf' ? 50 * 1024 * 1024 : 10 * 1024 * 1024;
                if ($file->getSize() > $maxSize) {
                    $label = $fileType === 'pdf' ? '50MB' : '10MB';
                    $validator->errors()->add('file', "File size must not exceed {$label}.");
                }
            }
        });
    }
}
