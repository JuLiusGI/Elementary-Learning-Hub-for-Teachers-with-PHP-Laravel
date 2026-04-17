<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('students.index') }}" class="text-[#666666] hover:text-[#333333] mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-[#333333]">Add New Student</h2>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#333333] mb-4">Personal Information</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <x-input-label for="lrn" value="Learner Reference Number (LRN)" />
                        <x-text-input id="lrn" name="lrn" type="text" class="mt-1 block w-full" :value="old('lrn')" required maxlength="12" placeholder="12-digit LRN" />
                        <x-input-error :messages="$errors->get('lrn')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="first_name" value="First Name" />
                        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name')" required />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="middle_name" value="Middle Name" />
                        <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" :value="old('middle_name')" />
                        <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="last_name" value="Last Name" />
                        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name')" required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="suffix" value="Suffix (optional)" />
                        <x-text-input id="suffix" name="suffix" type="text" class="mt-1 block w-full" :value="old('suffix')" placeholder="Jr., III, etc." />
                        <x-input-error :messages="$errors->get('suffix')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="date_of_birth" value="Date of Birth" />
                        <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth')" required />
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="gender" value="Gender" />
                        <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm" required>
                            <option value="">Select gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>

                    <div class="col-span-2" x-data="{ preview: null }">
                        <x-input-label for="photo" value="Photo (optional)" />
                        <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-[#666666] file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20" x-on:change="preview = URL.createObjectURL($event.target.files[0])">
                        <x-input-error :messages="$errors->get('photo')" class="mt-2" />
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
                        <x-text-input id="address_street" name="address_street" type="text" class="mt-1 block w-full" :value="old('address_street')" />
                    </div>

                    <div>
                        <x-input-label for="address_barangay" value="Barangay" />
                        <x-text-input id="address_barangay" name="address_barangay" type="text" class="mt-1 block w-full" :value="old('address_barangay')" />
                    </div>

                    <div>
                        <x-input-label for="address_municipality" value="Municipality" />
                        <x-text-input id="address_municipality" name="address_municipality" type="text" class="mt-1 block w-full" :value="old('address_municipality')" />
                    </div>

                    <div>
                        <x-input-label for="address_province" value="Province" />
                        <x-text-input id="address_province" name="address_province" type="text" class="mt-1 block w-full" :value="old('address_province')" />
                    </div>
                </div>
            </div>

            <!-- Guardian Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#333333] mb-4">Guardian Information</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="guardian_name" value="Guardian Name" />
                        <x-text-input id="guardian_name" name="guardian_name" type="text" class="mt-1 block w-full" :value="old('guardian_name')" required />
                        <x-input-error :messages="$errors->get('guardian_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="guardian_contact" value="Contact Number (optional)" />
                        <x-text-input id="guardian_contact" name="guardian_contact" type="text" class="mt-1 block w-full" :value="old('guardian_contact')" placeholder="09XX-XXX-XXXX" />
                        <x-input-error :messages="$errors->get('guardian_contact')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="guardian_relationship" value="Relationship" />
                        <select id="guardian_relationship" name="guardian_relationship" class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm" required>
                            <option value="">Select relationship</option>
                            <option value="mother" {{ old('guardian_relationship') === 'mother' ? 'selected' : '' }}>Mother</option>
                            <option value="father" {{ old('guardian_relationship') === 'father' ? 'selected' : '' }}>Father</option>
                            <option value="guardian" {{ old('guardian_relationship') === 'guardian' ? 'selected' : '' }}>Guardian</option>
                            <option value="grandparent" {{ old('guardian_relationship') === 'grandparent' ? 'selected' : '' }}>Grandparent</option>
                            <option value="other" {{ old('guardian_relationship') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <x-input-error :messages="$errors->get('guardian_relationship')" class="mt-2" />
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
                                <option value="">Select grade level</option>
                                @foreach($gradeLevels as $key => $label)
                                    <option value="{{ $key }}" {{ old('grade_level') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        @endif
                        <x-input-error :messages="$errors->get('grade_level')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="date_enrolled" value="Date Enrolled" />
                        <x-text-input id="date_enrolled" name="date_enrolled" type="date" class="mt-1 block w-full" :value="old('date_enrolled', now()->format('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('date_enrolled')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="previous_school" value="Previous School (optional)" />
                        <x-text-input id="previous_school" name="previous_school" type="text" class="mt-1 block w-full" :value="old('previous_school')" />
                    </div>

                    <div>
                        <x-input-label value="School Year" />
                        <x-text-input type="text" class="mt-1 block w-full bg-gray-100" :value="$currentSchoolYear->name ?? 'N/A'" disabled />
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#333333] mb-4">Additional Information</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="special_needs" value="Special Needs (optional)" />
                        <textarea id="special_needs" name="special_needs" rows="3" class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">{{ old('special_needs') }}</textarea>
                    </div>

                    <div>
                        <x-input-label for="medical_notes" value="Medical Notes (optional)" />
                        <textarea id="medical_notes" name="medical_notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">{{ old('medical_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('students.index') }}" class="px-4 py-2 text-sm font-medium text-[#666666] hover:text-[#333333] transition">Cancel</a>
                <x-primary-button>Save Student</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
