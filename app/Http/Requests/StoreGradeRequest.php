<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'exists:subjects,id'],
            'quarter' => ['required', 'in:Q1,Q2,Q3,Q4'],
            'grades' => ['required', 'array', 'min:1'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.ww_total_score' => ['nullable', 'numeric', 'min:0'],
            'grades.*.ww_max_score' => ['nullable', 'numeric', 'min:1'],
            'grades.*.pt_total_score' => ['nullable', 'numeric', 'min:0'],
            'grades.*.pt_max_score' => ['nullable', 'numeric', 'min:1'],
            'grades.*.qa_score' => ['nullable', 'numeric', 'min:0'],
            'grades.*.qa_max_score' => ['nullable', 'numeric', 'min:1'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $grades = $this->input('grades', []);
            foreach ($grades as $index => $grade) {
                if (isset($grade['ww_total_score'], $grade['ww_max_score']) && $grade['ww_total_score'] > $grade['ww_max_score']) {
                    $validator->errors()->add("grades.{$index}.ww_total_score", 'WW score cannot exceed max score.');
                }
                if (isset($grade['pt_total_score'], $grade['pt_max_score']) && $grade['pt_total_score'] > $grade['pt_max_score']) {
                    $validator->errors()->add("grades.{$index}.pt_total_score", 'PT score cannot exceed max score.');
                }
                if (isset($grade['qa_score'], $grade['qa_max_score']) && $grade['qa_score'] > $grade['qa_max_score']) {
                    $validator->errors()->add("grades.{$index}.qa_score", 'QA score cannot exceed max score.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'grades.*.ww_total_score.min' => 'Score cannot be negative.',
            'grades.*.pt_total_score.min' => 'Score cannot be negative.',
            'grades.*.qa_score.min' => 'Score cannot be negative.',
        ];
    }
}
