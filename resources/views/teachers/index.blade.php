<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-[#333333]">Teachers</h2>
            <a href="{{ route('teachers.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add Teacher
            </a>
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

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($teachers->isEmpty())
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <p class="text-sm text-[#666666]">No teachers found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Grade Level</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Students</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-[#666666] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($teachers as $teacher)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <a href="{{ route('teachers.show', $teacher) }}" class="text-sm font-medium text-[#333333] hover:text-primary">{{ $teacher->name }}</a>
                                </td>
                                <td class="px-6 py-3 text-sm text-[#666666]">{{ $teacher->email }}</td>
                                <td class="px-6 py-3 text-center text-sm text-[#666666]">{{ config('school.grade_levels')[$teacher->grade_level] ?? $teacher->grade_level }}</td>
                                <td class="px-6 py-3 text-center text-sm text-[#666666]">{{ $teacher->students_count }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if($teacher->is_active)
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Active</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('teachers.show', $teacher) }}" class="text-xs text-primary hover:underline">View</a>
                                        <a href="{{ route('teachers.edit', $teacher) }}" class="text-xs text-[#666666] hover:text-[#333333]">Edit</a>
                                        @if($teacher->is_active)
                                            <form method="POST" action="{{ route('teachers.destroy', $teacher) }}" x-data x-on:submit.prevent="if(confirm('Deactivate this teacher account?')) $el.submit()">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">Deactivate</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
