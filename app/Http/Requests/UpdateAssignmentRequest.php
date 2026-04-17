<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequest extends FormRequest
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
            'type' => ['sometimes', 'required', 'in:written_work,performance_task'],
            'max_score' => ['sometimes', 'required', 'numeric', 'min:1'],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
