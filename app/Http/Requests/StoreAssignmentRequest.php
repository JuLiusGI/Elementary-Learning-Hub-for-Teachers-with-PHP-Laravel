<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
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
            'type' => ['required', 'in:written_work,performance_task'],
            'max_score' => ['required', 'numeric', 'min:1'],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'grade_level' => ['nullable', 'string'],
        ];
    }
}
