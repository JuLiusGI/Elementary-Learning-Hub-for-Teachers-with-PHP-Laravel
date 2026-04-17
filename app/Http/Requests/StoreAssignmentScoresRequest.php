<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentScoresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scores' => ['required', 'array', 'min:1'],
            'scores.*.student_id' => ['required', 'exists:students,id'],
            'scores.*.score' => ['nullable', 'numeric', 'min:0'],
            'scores.*.remarks' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $assignment = $this->route('assignment');
            $scores = $this->input('scores', []);
            foreach ($scores as $index => $entry) {
                if (isset($entry['score']) && $entry['score'] !== null && $entry['score'] !== '') {
                    if ((float) $entry['score'] > (float) $assignment->max_score) {
                        $validator->errors()->add("scores.{$index}.score", 'Score cannot exceed max score of ' . $assignment->max_score . '.');
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'scores.*.score.min' => 'Score cannot be negative.',
        ];
    }
}
