<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('assignments.index') }}" class="text-[#666666] hover:text-[#333333]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h2 class="text-xl font-semibold text-[#333333]">{{ $assignment->title }}</h2>
                    <p class="text-sm text-[#666666]">{{ $assignment->subject->name }} &bull; {{ $assignment->quarter }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('assignments.scores', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Enter Scores
                </a>
                <a href="{{ route('assignments.edit', $assignment) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-[#666666] hover:bg-gray-50 transition">
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Assignment Info -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-4 gap-6">
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider">Type</p>
                @if($assignment->isWrittenWork())
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700 mt-1 inline-block">Written Work</span>
                @else
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700 mt-1 inline-block">Performance Task</span>
                @endif
            </div>
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider">Max Score</p>
                <p class="text-lg font-bold text-[#333333] mt-1">{{ number_format((float)$assignment->max_score, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider">Due Date</p>
                <p class="text-sm text-[#333333] mt-1">{{ $assignment->due_date?->format('M d, Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider">Created By</p>
                <p class="text-sm text-[#333333] mt-1">{{ $assignment->teacher->name }}</p>
            </div>
        </div>
        @if($assignment->description)
            <div class="mt-4 pt-4 border-t">
                <p class="text-xs text-[#666666] uppercase tracking-wider mb-1">Description</p>
                <p class="text-sm text-[#333333]">{{ $assignment->description }}</p>
            </div>
        @endif
    </div>

    <!-- Score Summary -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-primary">
            <p class="text-xs text-[#666666]">Scored</p>
            <p class="text-2xl font-bold text-[#333333]">{{ $assignment->scores->whereNotNull('score')->count() }}<span class="text-sm text-[#666666]">/{{ $totalStudents }}</span></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-secondary-dark">
            <p class="text-xs text-[#666666]">Average Score</p>
            @php $avg = $assignment->scores->whereNotNull('score')->avg('score'); @endphp
            <p class="text-2xl font-bold text-[#333333]">{{ $avg !== null ? number_format($avg, 2) : '—' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-accent">
            <p class="text-xs text-[#666666]">Average Rate</p>
            <p class="text-2xl font-bold text-[#333333]">{{ $avg !== null ? number_format(($avg / (float)$assignment->max_score) * 100, 1) . '%' : '—' }}</p>
        </div>
    </div>

    <!-- Scores Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-3 bg-gray-50 border-b">
            <h3 class="text-sm font-semibold text-[#333333]">Student Scores</h3>
        </div>
        @if($assignment->scores->isEmpty())
            <div class="text-center py-8">
                <p class="text-sm text-[#666666]">No scores recorded yet.</p>
                <a href="{{ route('assignments.scores', $assignment) }}" class="text-sm text-primary hover:underline mt-1 inline-block">Enter scores now</a>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider w-8">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($assignment->scores->sortBy('student.last_name') as $index => $score)
                        @php
                            $rate = $score->score !== null ? ((float)$score->score / (float)$assignment->max_score) * 100 : null;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm text-[#666666]">{{ $index + 1 }}</td>
                            <td class="px-6 py-3 text-sm font-medium text-[#333333]">{{ $score->student->full_name }}</td>
                            <td class="px-6 py-3 text-center">
                                @if($score->score !== null)
                                    <span class="text-sm font-medium {{ $rate >= 75 ? 'text-green-600' : ($rate < 50 ? 'text-red-600' : 'text-yellow-600') }}">
                                        {{ number_format((float)$score->score, 2) }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center">
                                @if($rate !== null)
                                    <span class="text-xs {{ $rate >= 75 ? 'text-green-600' : ($rate < 50 ? 'text-red-600' : 'text-yellow-600') }}">
                                        {{ number_format($rate, 1) }}%
                                    </span>
                                @else
                                    <span class="text-sm text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-sm text-[#666666]">{{ $score->remarks ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
