<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('approvals.index') }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-[#333333]">
                    Review — {{ config('school.grade_levels')[$gradeLevel] ?? $gradeLevel }}
                    @isset($subject) — {{ $subject->name }} @endisset
                    — {{ $quarter }}
                </h2>
            </div>
        </div>
    </x-slot>

    @if($gradeLevel === 'kinder')
        {{-- Kindergarten Assessment Review --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase">Student</th>
                        @foreach($domains as $domainKey => $domainLabel)
                            <th class="px-4 py-3 text-center text-xs font-medium text-[#666666] uppercase">{{ $domainLabel }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($assessments as $studentId => $studentAssessments)
                        @php $student = $studentAssessments->first()->student; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm font-medium text-[#333333]">{{ $student->full_name }}</td>
                            @foreach($domains as $domainKey => $domainLabel)
                                @php
                                    $assessment = $studentAssessments->firstWhere('domain', $domainKey);
                                    $ratingColors = [
                                        'beginning' => 'bg-yellow-100 text-yellow-800',
                                        'developing' => 'bg-blue-100 text-blue-800',
                                        'proficient' => 'bg-green-100 text-green-800',
                                    ];
                                @endphp
                                <td class="px-4 py-3 text-center">
                                    @if($assessment && $assessment->rating)
                                        <span class="px-2 py-0.5 text-[10px] font-medium rounded-full {{ $ratingColors[$assessment->rating] }}">
                                            {{ config('school.kinder_ratings')[$assessment->rating] }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        {{-- Numerical Grade Review --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase">Student</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-yellow-700 uppercase bg-yellow-50">WW (40%)</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-blue-700 uppercase bg-blue-50">PT (40%)</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-purple-700 uppercase bg-purple-50">QA (20%)</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-[#333333] uppercase bg-gray-100">Grade</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-[#333333] uppercase bg-gray-100">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($grades as $index => $grade)
                        <tr class="hover:bg-gray-50 {{ (float)$grade->quarterly_grade < 75 ? 'bg-red-50/50' : '' }}">
                            <td class="px-6 py-3 text-sm text-[#666666]">{{ $index + 1 }}</td>
                            <td class="px-6 py-3 text-sm font-medium text-[#333333]">{{ $grade->student->full_name }}</td>
                            <td class="px-4 py-3 text-center text-sm bg-yellow-50/30">
                                {{ $grade->ww_total_score }}/{{ $grade->ww_max_score }}
                                <span class="block text-[10px] text-[#666666]">{{ $grade->ww_weighted }}%</span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm bg-blue-50/30">
                                {{ $grade->pt_total_score }}/{{ $grade->pt_max_score }}
                                <span class="block text-[10px] text-[#666666]">{{ $grade->pt_weighted }}%</span>
                            </td>
                            <td class="px-4 py-3 text-center text-sm bg-purple-50/30">
                                {{ $grade->qa_score }}/{{ $grade->qa_max_score }}
                                <span class="block text-[10px] text-[#666666]">{{ $grade->qa_weighted }}%</span>
                            </td>
                            <td class="px-4 py-3 text-center bg-gray-50">
                                <span class="text-sm font-bold {{ (float)$grade->quarterly_grade >= 75 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format((float)$grade->quarterly_grade, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center bg-gray-50">
                                <span class="text-xs font-medium {{ $grade->remarks === 'Passed' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $grade->remarks }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="mt-6 flex items-center justify-between">
        <a href="{{ route('approvals.index') }}" class="text-sm text-[#666666] hover:text-[#333333]">Back to list</a>

        <div class="flex items-center gap-3" x-data="{ showRejectModal: false }">
            <!-- Reject Button -->
            <button @click="showRejectModal = true" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-md hover:bg-red-200 transition">
                Return for Revision
            </button>

            <!-- Approve Button -->
            <form method="POST" action="{{ route('approvals.approve', [$gradeLevel, $subject->id ?? 'kinder', $quarter]) }}" x-data x-on:submit.prevent="if(confirm('Approve these grades? This action cannot be undone.')) $el.submit()">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Approve
                </button>
            </form>

            <!-- Reject Modal -->
            <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6" @click.away="showRejectModal = false">
                    <h3 class="text-lg font-semibold text-[#333333] mb-4">Return for Revision</h3>
                    <form method="POST" action="{{ route('approvals.reject', [$gradeLevel, $subject->id ?? 'kinder', $quarter]) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-[#666666] mb-1">Reason</label>
                            <textarea name="reason" rows="3" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm" placeholder="Explain why these grades need revision..."></textarea>
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" @click="showRejectModal = false" class="text-sm text-[#666666]">Cancel</button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                                Return for Revision
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
