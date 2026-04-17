<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Student::class);
    }

    protected function prepareForValidation(): void
    {
        $user = $this->user();
        if ($user->isTeacher()) {
            $this->merge(['grade_level' => $user->grade_level]);
        }
    }

    public function rules(): array
    {
        return [
            'lrn' => ['required', 'string', 'size:12', 'regex:/^\d{12}$/', 'unique:students,lrn'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_barangay' => ['nullable', 'string', 'max:255'],
            'address_municipality' => ['nullable', 'string', 'max:255'],
            'address_province' => ['nullable', 'string', 'max:255'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'guardian_contact' => ['nullable', 'string', 'max:20'],
            'guardian_relationship' => ['required', 'in:mother,father,guardian,grandparent,other'],
            'special_needs' => ['nullable', 'string'],
            'medical_notes' => ['nullable', 'string'],
            'grade_level' => ['required', 'in:' . implode(',', array_keys(config('school.grade_levels')))],
            'date_enrolled' => ['required', 'date'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'lrn.size' => 'The LRN must be exactly 12 digits.',
            'lrn.regex' => 'The LRN must contain only digits.',
            'lrn.unique' => 'This LRN is already registered.',
        ];
    }
}
