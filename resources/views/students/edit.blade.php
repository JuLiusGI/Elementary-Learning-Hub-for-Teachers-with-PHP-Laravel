<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('students.show', $student) }}" class="text-[#666666] hover:text-[#333333] mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-[#333333]">Edit Student</h2>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#333333] mb-4">Personal Information</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <x-input-label for="lrn" value="Learner Reference Number (LRN)" />
                        <x-text-input id="lrn" name="lrn" type="text" class="mt-1 block w-full" :value="old('lrn', $student->lrn)" required maxlength="12" />
                        <x-input-error :messages="$errors->get('lrn')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="first_name" value="First Name" />
                        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $student->first_name)" required />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="middle_name" value="Middle Name" />
                        <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" :value="old('middle_name', $student->middle_name)" />
                    </div>

                    <div>
                        <x-input-label for="last_name" value="Last Name" />
                        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $student->last_name)" required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="suffix" value="Suffix (optional)" />
                        <x-text-input id="suffix" name="suffix" type="text" class="mt-1 block w-full" :value="old('suffix', $student->suffix)" />
                    </div>

                    <div>
                        <x-input-label for="date_of_birth" value="Date of Birth" />
                        <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth', $student->date_of_birth->format('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="gender" value="Gender" />
                        <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm" required>
                            <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>

                    <div class="col-span-2" x-data="{ preview: null }">
                        <x-input-label for="photo" value="Photo" />
                        @if($student->photo_path)
                            <div class="mt-2 mb-2">
                                <img src="{{ Storage::url($student->photo_path) }}" alt="Current photo" class="w-24 h-24 rounded-lg object-cover">
                                <p class="text-xs text-[#666666] mt-1">Current photo. Upload a new one to replace it.</p>
                            </div>
                        @endif
                        <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-[#666666] file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20" x-on:change="preview = URL.createObjectURL($event.target.files[0])">
                        <img x-show="preview" :src="preview" class="mt-2 w-24 h-24 rounded-lg object-cover" x-cloak>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#333333] mb-4">Address</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <x-input-label for="address_street" value="Street/Purok (optional)" />
                        <x-text-input id="address_street" name="address_street" type="text" class="mt-1 block w-full" :value="old('address_street', $student->address_street)" />
                    </div>

                    <div>
                        <x-input-label for="address_barangay" value="Barangay" />
                        <x-text-input id="address_barangay" name="address_barangay" type="text" class="mt-1 block w-full" :value="old('address_barangay', $student->address_barangay)" />
                    </div>

                    <div>
                        <x-input-label for="address_municipality" value="Municipality" />
                        <x-text-input id="address_municipality" name="address_municipality" type="text" class="mt-1 block w-full" :value="old('address_municipality', $student->address_municipality)" />
                    </div>

                    <div>
                        <x-input-label for="address_province" value="Province" />
                        <x-text-input id="address_province" name="address_province" type="text" class="mt-1 block w-full" :value="old('address_province', $student->address_province)" />
                    </div>
                </div>
            </div>

            <!-- Guardian Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#333333] mb-4">Guardian Information</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="guardian_name" value="Guardian Name" />
                        <x-text-input id="guardian_name" name="guardian_name" type="text" class="mt-1 block w-full" :value="old('guardian_name', $student->guardian_name)" required />
                        <x-input-error :messages="$errors->get('guardian_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="guardian_contact" value="Contact Number (optional)" />
                        <x-text-input id="guardian_contact" name="guardian_contact" type="text" class="mt-1 block w-full" :value="old('guardian_contact', $student->guardian_contact)" />
                    </div>

                    <div>
                        <x-input-label for="guardian_relationship" value="Relationship" />
                        <select id="guardian_relationship" name="guardian_relationship" class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm" required>
                            @foreach(['mother', 'father', 'guardian', 'grandparent', 'other'] as $rel)
                                <option value="{{ $rel }}" {{ old('guardian_relationship', $student->guardian_relationship) === $rel ? 'selected' : '' }}>{{ ucfirst($rel) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#333333] mb-4">Academic Information</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="grade_level" value="Grade Level" />
                        @if(auth()->user()->isTeacher())
                            <x-text-input type="text" class="mt-1 block w-full bg-gray-100" :value="$gradeLevels[auth()->user()->grade_level]" disabled />
                            <input type="hidden" name="grade_level" value="{{ auth()->user()->grade_level }}">
                        @else
                            <select id="grade_level" name="grade_level" class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm" required>
                                @foreach($gradeLevels as $key => $label)
                                    <option value="{{ $key }}" {{ old('grade_level', $student->grade_level) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <x-input-label for="date_enrolled" value="Date Enrolled" />
                        <x-text-input id="date_enrolled" name="date_enrolled" type="date" class="mt-1 block w-full" :value="old('date_enrolled', $student->date_enrolled->format('Y-m-d'))" required />
                    </div>

                    <div>
                        <x-input-label for="enrollment_status" value="Enrollment Status" />
                        <select id="enrollment_status" name="enrollment_status" class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
                            <option value="active" {{ old('enrollment_status', $student->enrollment_status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="transferred" {{ old('enrollment_status', $student->enrollment_status) === 'transferred' ? 'selected' : '' }}>Transferred</option>
                            <option value="dropped" {{ old('enrollment_status', $student->enrollment_status) === 'dropped' ? 'selected' : '' }}>Dropped</option>
                            <option value="graduated" {{ old('enrollment_status', $student->enrollment_status) === 'graduated' ? 'selected' : '' }}>Graduated</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="previous_school" value="Previous School (optional)" />
                        <x-text-input id="previous_school" name="previous_school" type="text" class="mt-1 block w-full" :value="old('previous_school', $student->previous_school)" />
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#333333] mb-4">Additional Information</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="special_needs" value="Special Needs (optional)" />
                        <textarea id="special_needs" name="special_needs" rows="3" class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">{{ old('special_needs', $student->special_needs) }}</textarea>
                    </div>

                    <div>
                        <x-input-label for="medical_notes" value="Medical Notes (optional)" />
                        <textarea id="medical_notes" name="medical_notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">{{ old('medical_notes', $student->medical_notes) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('students.show', $student) }}" class="px-4 py-2 text-sm font-medium text-[#666666] hover:text-[#333333] transition">Cancel</a>
                <x-primary-button>Update Student</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
