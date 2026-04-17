<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('students.index') }}" class="text-[#666666] hover:text-[#333333] mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="text-xl font-semibold text-[#333333]">Student Profile</h2>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('reports.sf9', $student) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-[#666666] hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    SF9
                </a>
                <a href="{{ route('reports.sf10', $student) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-[#666666] hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    SF10
                </a>
                <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary-dark transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit
                </a>
                @can('delete', $student)
                    <form method="POST" action="{{ route('students.destroy', $student) }}" x-data x-on:submit.prevent="if(confirm('Are you sure you want to delete this student? This action cannot be undone.')) $el.submit()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-accent text-white text-sm font-medium rounded-md hover:bg-accent-dark transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                @if($student->photo_path)
                    <img src="{{ Storage::url($student->photo_path) }}" alt="{{ $student->first_name }}" class="w-32 h-32 rounded-full object-cover mx-auto">
                @else
                    <div class="w-32 h-32 rounded-full bg-primary/10 flex items-center justify-center mx-auto">
                        <span class="text-3xl font-bold text-primary">{{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}</span>
                    </div>
                @endif

                <h3 class="mt-4 text-lg font-semibold text-[#333333]">{{ $student->full_name }}</h3>
                <p class="text-sm text-[#666666]">{{ $student->grade_level_label }}</p>
                <p class="text-sm font-mono text-[#666666] mt-1">LRN: {{ $student->lrn }}</p>

                @php
                    $statusColors = [
                        'active' => 'bg-green-100 text-green-800',
                        'transferred' => 'bg-yellow-100 text-yellow-800',
                        'dropped' => 'bg-red-100 text-red-800',
                        'graduated' => 'bg-blue-100 text-blue-800',
                    ];
                @endphp
                <span class="mt-3 inline-block px-3 py-1 text-xs font-medium rounded-full {{ $statusColors[$student->enrollment_status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($student->enrollment_status) }}
                </span>
            </div>
        </div>

        <!-- Details -->
        <div class="col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#333333] mb-4">Personal Information</h3>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-[#666666]">Date of Birth</dt>
                        <dd class="mt-1 text-sm text-[#333333]">{{ $student->date_of_birth->format('F d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-[#666666]">Gender</dt>
                        <dd class="mt-1 text-sm text-[#333333] capitalize">{{ $student->gender }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-sm font-medium text-[#666666]">Address</dt>
                        <dd class="mt-1 text-sm text-[#333333]">
                            {{ collect([$student->address_street, $student->address_barangay, $student->address_municipality, $student->address_province])->filter()->implode(', ') ?: 'Not provided' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Guardian Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#333333] mb-4">Guardian Information</h3>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-[#666666]">Name</dt>
                        <dd class="mt-1 text-sm text-[#333333]">{{ $student->guardian_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-[#666666]">Relationship</dt>
                        <dd class="mt-1 text-sm text-[#333333] capitalize">{{ $student->guardian_relationship }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-[#666666]">Contact Number</dt>
                        <dd class="mt-1 text-sm text-[#333333]">{{ $student->guardian_contact ?: 'Not provided' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Academic Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#333333] mb-4">Academic Information</h3>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-[#666666]">School Year</dt>
                        <dd class="mt-1 text-sm text-[#333333]">{{ $student->schoolYear->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-[#666666]">Date Enrolled</dt>
                        <dd class="mt-1 text-sm text-[#333333]">{{ $student->date_enrolled->format('F d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-[#666666]">Teacher</dt>
                        <dd class="mt-1 text-sm text-[#333333]">{{ $student->teacher->name ?? 'Unassigned' }}</dd>
                    </div>
                    @if($student->previous_school)
                        <div>
                            <dt class="text-sm font-medium text-[#666666]">Previous School</dt>
                            <dd class="mt-1 text-sm text-[#333333]">{{ $student->previous_school }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Additional Notes -->
            @if($student->special_needs || $student->medical_notes)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-[#333333] mb-4">Additional Notes</h3>
                    <dl class="space-y-4">
                        @if($student->special_needs)
                            <div>
                                <dt class="text-sm font-medium text-[#666666]">Special Needs</dt>
                                <dd class="mt-1 text-sm text-[#333333]">{{ $student->special_needs }}</dd>
                            </div>
                        @endif
                        @if($student->medical_notes)
                            <div>
                                <dt class="text-sm font-medium text-[#666666]">Medical Notes</dt>
                                <dd class="mt-1 text-sm text-[#333333]">{{ $student->medical_notes }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
