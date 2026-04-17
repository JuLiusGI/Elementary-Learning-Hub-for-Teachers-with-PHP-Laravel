<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-[#333333]">Student Promotions</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- School Year Selector -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('promotions.index') }}" class="flex items-center gap-4">
            <label class="text-sm font-medium text-[#333333]">School Year:</label>
            <select name="school_year_id" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                @foreach($schoolYears as $sy)
                    <option value="{{ $sy->id }}" {{ $selectedSchoolYear && $selectedSchoolYear->id == $sy->id ? 'selected' : '' }}>
                        {{ $sy->name }} {{ $sy->is_current ? '(Current)' : '' }}
                    </option>
                @endforeach
            </select>
            <x-primary-button>View</x-primary-button>
            <a href="{{ route('promotions.history') }}" class="text-sm text-primary hover:underline ml-auto">View History</a>
        </form>
    </div>

    @if($selectedSchoolYear)
        @if(empty($candidates))
            <div class="bg-white rounded-lg shadow-sm text-center py-12">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                <p class="text-sm text-[#666666]">No active students found for {{ $selectedSchoolYear->name }}.</p>
            </div>
        @else
            <!-- Grade Level Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($candidates as $gradeLevel => $students)
                    @php
                        $totalStudents = count($students);
                        $promoted = collect($students)->where('recommendation', 'promoted')->count();
                        $graduated = collect($students)->where('recommendation', 'graduated')->count();
                        $retained = collect($students)->where('recommendation', 'retained')->count();
                        $pending = collect($students)->where('recommendation', 'pending')->count();
                        $alreadyProcessed = collect($students)->whereNotNull('promotion')->count();
                        $averages = collect($students)->pluck('general_average')->filter()->values();
                        $classAvg = $averages->count() > 0 ? round($averages->avg(), 2) : null;
                    @endphp
                    <div class="bg-white rounded-lg shadow-sm p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-[#333333]">{{ config('school.grade_levels')[$gradeLevel] ?? $gradeLevel }}</h3>
                            <span class="text-xs text-[#666666]">{{ $totalStudents }} students</span>
                        </div>

                        <div class="space-y-2 text-sm mb-4">
                            @if($classAvg !== null)
                                <div class="flex justify-between">
                                    <span class="text-[#666666]">Class Average:</span>
                                    <span class="font-medium {{ $classAvg >= 75 ? 'text-green-700' : 'text-red-700' }}">{{ number_format($classAvg, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-[#666666]">Recommended Promote:</span>
                                <span class="font-medium text-green-700">{{ $promoted + $graduated }}</span>
                            </div>
                            @if($retained > 0)
                                <div class="flex justify-between">
                                    <span class="text-[#666666]">Recommended Retain:</span>
                                    <span class="font-medium text-red-700">{{ $retained }}</span>
                                </div>
                            @endif
                            @if($pending > 0)
                                <div class="flex justify-between">
                                    <span class="text-[#666666]">Incomplete Grades:</span>
                                    <span class="font-medium text-yellow-700">{{ $pending }}</span>
                                </div>
                            @endif
                            @if($alreadyProcessed > 0)
                                <div class="flex justify-between">
                                    <span class="text-[#666666]">Already Processed:</span>
                                    <span class="font-medium text-blue-700">{{ $alreadyProcessed }}</span>
                                </div>
                            @endif
                        </div>

                        @if($alreadyProcessed < $totalStudents)
                            <a href="{{ route('promotions.review', ['school_year_id' => $selectedSchoolYear->id, 'grade_level' => $gradeLevel]) }}"
                               class="block w-full text-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                                Review & Promote
                            </a>
                        @else
                            <span class="block w-full text-center px-4 py-2 bg-gray-100 text-[#666666] text-sm font-medium rounded-md">All Processed</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</x-app-layout>
