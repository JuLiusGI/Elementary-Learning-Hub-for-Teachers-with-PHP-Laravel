<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('grades.index') }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-[#333333]">Grade Summary — {{ $student->full_name }}</h2>
                <p class="text-sm text-[#666666]">{{ $student->grade_level_label }} &bull; LRN: {{ $student->lrn }}</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Subject</th>
                        @foreach(config('school.quarters') as $quarter)
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">{{ $quarter }}</th>
                        @endforeach
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#333333] uppercase tracking-wider bg-gray-100">Final</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#333333] uppercase tracking-wider bg-gray-100">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php $allFinalGrades = []; @endphp
                    @foreach($subjects as $subject)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <p class="text-sm font-medium text-[#333333]">{{ $subject->name }}</p>
                                <p class="text-xs text-[#666666]">{{ $subject->code }}</p>
                            </td>
                            @foreach(config('school.quarters') as $quarter)
                                @php
                                    $grade = ($grades->get($subject->id) ?? collect())->firstWhere('quarter', $quarter);
                                @endphp
                                <td class="px-6 py-3 text-center">
                                    @if($grade && $grade->quarterly_grade !== null)
                                        <span class="text-sm font-medium {{ (float)$grade->quarterly_grade >= 75 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format((float)$grade->quarterly_grade, 2) }}
                                        </span>
                                        @php
                                            $statusBadge = match($grade->status) {
                                                'draft' => 'bg-yellow-100 text-yellow-700',
                                                'submitted' => 'bg-blue-100 text-blue-700',
                                                'approved' => 'bg-green-100 text-green-700',
                                                'locked' => 'bg-gray-100 text-gray-600',
                                                default => '',
                                            };
                                        @endphp
                                        <span class="block text-[10px] mt-0.5 px-1.5 py-0.5 rounded-full inline-block {{ $statusBadge }}">{{ ucfirst($grade->status) }}</span>
                                    @else
                                        <span class="text-sm text-gray-300">—</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-6 py-3 text-center bg-gray-50">
                                @if($finalGrades[$subject->id]['grade'] !== null)
                                    @php $allFinalGrades[] = $finalGrades[$subject->id]['grade']; @endphp
                                    <span class="text-sm font-bold {{ $finalGrades[$subject->id]['grade'] >= 75 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($finalGrades[$subject->id]['grade'], 2) }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center bg-gray-50">
                                @if($finalGrades[$subject->id]['remarks'])
                                    <span class="text-xs font-medium {{ $finalGrades[$subject->id]['remarks'] === 'Passed' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $finalGrades[$subject->id]['remarks'] }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                @if(count($allFinalGrades) > 0)
                    <tfoot class="bg-primary/5 border-t-2 border-primary">
                        <tr>
                            <td class="px-6 py-3 text-sm font-bold text-primary">General Average</td>
                            <td colspan="4" class="px-6 py-3"></td>
                            @php
                                $genAvg = count($allFinalGrades) === $subjects->count() ? round(array_sum($allFinalGrades) / count($allFinalGrades), 2) : null;
                            @endphp
                            <td class="px-6 py-3 text-center bg-gray-50">
                                @if($genAvg !== null)
                                    <span class="text-sm font-bold {{ $genAvg >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($genAvg, 2) }}</span>
                                @else
                                    <span class="text-sm text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center bg-gray-50">
                                @if($genAvg !== null)
                                    <span class="text-xs font-bold {{ $genAvg >= 75 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $genAvg >= 75 ? 'Passed' : 'Failed' }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-4 flex justify-end gap-2">
        <a href="{{ route('reports.sf9', $student) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Generate SF9
        </a>
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-[#666666] hover:bg-gray-50 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print
        </button>
    </div>
</x-app-layout>
