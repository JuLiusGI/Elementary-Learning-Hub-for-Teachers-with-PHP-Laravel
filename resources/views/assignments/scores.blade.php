<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('assignments.show', $assignment) }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-[#333333]">Score Entry — {{ $assignment->title }}</h2>
                <p class="text-sm text-[#666666]">{{ $assignment->subject->name }} &bull; {{ $assignment->quarter }} &bull; Max Score: {{ number_format((float)$assignment->max_score, 2) }}</p>
            </div>
        </div>
    </x-slot>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-red-600">Please correct the errors below.</p>
        </div>
    @endif

    <!-- Info Banner -->
    <div class="bg-secondary/50 border border-secondary-dark rounded-lg p-3 mb-6">
        <div class="flex items-center gap-6 text-xs text-[#666666]">
            <span>
                Type:
                @if($assignment->isWrittenWork())
                    <strong class="text-yellow-700">Written Work (WW)</strong>
                @else
                    <strong class="text-blue-700">Performance Task (PT)</strong>
                @endif
            </span>
            <span>Max Score: <strong class="text-[#333333]">{{ number_format((float)$assignment->max_score, 2) }}</strong></span>
            @if($assignment->due_date)
                <span>Due: <strong class="text-[#333333]">{{ $assignment->due_date->format('M d, Y') }}</strong></span>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('assignments.scores.store', $assignment) }}">
        @csrf

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider w-8">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider min-w-[200px]">Student</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider w-32">Score</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($students as $index => $student)
                            @php $existing = $existingScores->get($student->id); @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-[#666666]">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">
                                    <input type="hidden" name="scores[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    <span class="text-sm font-medium text-[#333333]">{{ $student->full_name }}</span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <input type="number" name="scores[{{ $index }}][score]" value="{{ old("scores.{$index}.score", $existing?->score) }}" step="0.01" min="0" max="{{ $assignment->max_score }}" class="w-24 text-center border-gray-300 focus:border-primary focus:ring-primary rounded text-sm" placeholder="—">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" name="scores[{{ $index }}][remarks]" value="{{ old("scores.{$index}.remarks", $existing?->remarks) }}" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded text-sm" placeholder="Optional remarks">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
                <a href="{{ route('assignments.show', $assignment) }}" class="text-sm text-[#666666] hover:text-[#333333]">Cancel</a>
                <x-primary-button>Save Scores</x-primary-button>
            </div>
        </div>
    </form>
</x-app-layout>
