<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('teachers.index') }}" class="text-[#666666] hover:text-[#333333]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="text-xl font-semibold text-[#333333]">Teacher Profile</h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('teachers.edit', $teacher) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-sm font-medium rounded-md text-[#333333] hover:bg-gray-50 transition">Edit</a>
                @if($teacher->is_active)
                    <form method="POST" action="{{ route('teachers.destroy', $teacher) }}" x-data x-on:submit.prevent="if(confirm('Deactivate this teacher account?')) $el.submit()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition">Deactivate</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Teacher Info Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider mb-1">Name</p>
                <p class="text-sm font-medium text-[#333333]">{{ $teacher->name }}</p>
            </div>
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider mb-1">Email</p>
                <p class="text-sm text-[#333333]">{{ $teacher->email }}</p>
            </div>
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider mb-1">Grade Level</p>
                <p class="text-sm text-[#333333]">{{ config('school.grade_levels')[$teacher->grade_level] ?? $teacher->grade_level }}</p>
            </div>
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider mb-1">Status</p>
                @if($teacher->is_active)
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Active</span>
                @else
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Inactive</span>
                @endif
            </div>
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider mb-1">Active Students</p>
                <p class="text-sm font-medium text-[#333333]">{{ $teacher->students_count }}</p>
            </div>
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider mb-1">Last Login</p>
                <p class="text-sm text-[#333333]">{{ $teacher->last_login_at?->format('M d, Y g:i A') ?? 'Never' }}</p>
            </div>
        </div>
    </div>

    <!-- Assigned Students -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-sm font-semibold text-[#333333]">Assigned Students ({{ $students->count() }})</h3>
        </div>
        @if($students->isEmpty())
            <div class="text-center py-8">
                <p class="text-sm text-[#666666]">No active students assigned.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">LRN</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Gender</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($students as $index => $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-[#666666]">{{ $index + 1 }}</td>
                                <td class="px-6 py-3">
                                    <a href="{{ route('students.show', $student) }}" class="text-sm font-medium text-[#333333] hover:text-primary">{{ $student->full_name }}</a>
                                </td>
                                <td class="px-6 py-3 text-sm text-[#666666]">{{ $student->lrn }}</td>
                                <td class="px-6 py-3 text-center text-sm text-[#666666]">{{ ucfirst($student->gender) }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">{{ ucfirst($student->enrollment_status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
