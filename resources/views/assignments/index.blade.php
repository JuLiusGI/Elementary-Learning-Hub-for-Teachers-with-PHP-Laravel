<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-[#333333]">Assignments</h2>
            <a href="{{ route('assignments.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Create Assignment
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('assignments.index') }}" class="flex items-center gap-4">
            @if(auth()->user()->isHeadTeacher())
                <select name="grade_level" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                    <option value="">All Grade Levels</option>
                    @foreach(config('school.grade_levels') as $key => $label)
                        <option value="{{ $key }}" {{ request('grade_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            @endif
            <select name="subject_id" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                @endforeach
            </select>
            <select name="quarter" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                <option value="">All Quarters</option>
                @foreach(config('school.quarters') as $q)
                    <option value="{{ $q }}" {{ request('quarter') == $q ? 'selected' : '' }}>{{ $q }}</option>
                @endforeach
            </select>
            <select name="type" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                <option value="">All Types</option>
                <option value="written_work" {{ request('type') == 'written_work' ? 'selected' : '' }}>Written Work</option>
                <option value="performance_task" {{ request('type') == 'performance_task' ? 'selected' : '' }}>Performance Task</option>
            </select>
            <x-primary-button>Filter</x-primary-button>
            @if(request()->hasAny(['subject_id', 'quarter', 'type', 'grade_level']))
                <a href="{{ route('assignments.index') }}" class="text-sm text-[#666666] hover:text-[#333333]">Clear</a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Assignments Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($assignments->isEmpty())
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <p class="text-sm text-[#666666]">No assignments found.</p>
                <a href="{{ route('assignments.create') }}" class="text-sm text-primary hover:underline mt-1 inline-block">Create your first assignment</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Quarter</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Max Score</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Scores</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-[#666666] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($assignments as $assignment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <a href="{{ route('assignments.show', $assignment) }}" class="text-sm font-medium text-[#333333] hover:text-primary">{{ $assignment->title }}</a>
                                </td>
                                <td class="px-6 py-3 text-sm text-[#666666]">{{ $assignment->subject->name }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if($assignment->isWrittenWork())
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">WW</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">PT</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center text-sm text-[#666666]">{{ $assignment->quarter }}</td>
                                <td class="px-6 py-3 text-center text-sm text-[#666666]">{{ number_format((float)$assignment->max_score, 2) }}</td>
                                <td class="px-6 py-3 text-center text-sm text-[#666666]">{{ $assignment->due_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="px-6 py-3 text-center text-sm text-[#666666]">{{ $assignment->scores_count }}</td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('assignments.scores', $assignment) }}" class="text-xs text-primary hover:underline">Scores</a>
                                        <a href="{{ route('assignments.edit', $assignment) }}" class="text-xs text-[#666666] hover:text-[#333333]">Edit</a>
                                        <form method="POST" action="{{ route('assignments.destroy', $assignment) }}" x-data x-on:submit.prevent="if(confirm('Delete this assignment? All scores will be lost.')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
