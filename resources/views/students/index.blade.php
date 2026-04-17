<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-[#333333]">Students</h2>
            <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary-dark transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add Student
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('students.index') }}" class="flex items-center gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or LRN..." class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
            </div>

            @if(auth()->user()->isHeadTeacher())
                <select name="grade_level" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
                    <option value="">All Grade Levels</option>
                    @foreach(config('school.grade_levels') as $key => $label)
                        <option value="{{ $key }}" {{ request('grade_level') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            @endif

            <select name="status" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="transferred" {{ request('status') === 'transferred' ? 'selected' : '' }}>Transferred</option>
                <option value="dropped" {{ request('status') === 'dropped' ? 'selected' : '' }}>Dropped</option>
                <option value="graduated" {{ request('status') === 'graduated' ? 'selected' : '' }}>Graduated</option>
            </select>

            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary-dark transition">
                Search
            </button>

            @if(request()->hasAny(['search', 'grade_level', 'status']))
                <a href="{{ route('students.index') }}" class="text-sm text-[#666666] hover:text-[#333333]">Clear</a>
            @endif
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($students->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">LRN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Grade Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Gender</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#666666] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-mono text-[#333333]">{{ $student->lrn }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($student->photo_path)
                                        <img src="{{ Storage::url($student->photo_path) }}" alt="" class="w-8 h-8 rounded-full object-cover mr-3">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                                            <span class="text-xs font-medium text-primary">{{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-[#333333]">{{ $student->full_name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#666666]">{{ $student->grade_level_label }}</td>
                            <td class="px-6 py-4 text-sm text-[#666666] capitalize">{{ $student->gender }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'transferred' => 'bg-yellow-100 text-yellow-800',
                                        'dropped' => 'bg-red-100 text-red-800',
                                        'graduated' => 'bg-blue-100 text-blue-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$student->enrollment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($student->enrollment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                <a href="{{ route('students.show', $student) }}" class="text-primary hover:text-primary-dark font-medium">View</a>
                                <a href="{{ route('students.edit', $student) }}" class="text-primary hover:text-primary-dark font-medium">Edit</a>
                                @can('delete', $student)
                                    <form method="POST" action="{{ route('students.destroy', $student) }}" class="inline" x-data x-on:submit.prevent="if(confirm('Are you sure you want to delete this student? This action cannot be undone.')) $el.submit()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-accent hover:text-accent-dark font-medium">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t">
                {{ $students->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <p class="mt-4 text-[#666666]">No students found.</p>
                <a href="{{ route('students.create') }}" class="mt-2 inline-flex items-center text-sm text-primary hover:text-primary-dark font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add your first student
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
