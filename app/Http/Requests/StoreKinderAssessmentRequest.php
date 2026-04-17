<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKinderAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isHeadTeacher() || $this->user()->grade_level === 'kinder';
    }

    public function rules(): array
    {
        $domains = implode(',', array_keys(config('school.kinder_domains')));
        $ratings = implode(',', array_keys(config('school.kinder_ratings')));

        return [
            'quarter' => ['required', 'in:Q1,Q2,Q3,Q4'],
            'assessments' => ['required', 'array', 'min:1'],
            'assessments.*.student_id' => ['required', 'exists:students,id'],
            'assessments.*.domains' => ['required', 'array'],
            'assessments.*.domains.*.domain' => ['required', "in:{$domains}"],
            'assessments.*.domains.*.rating' => ['nullable', "in:{$ratings}"],
            'assessments.*.domains.*.remarks' => ['nullable', 'string', 'max:500'],
        ];
    }
}
