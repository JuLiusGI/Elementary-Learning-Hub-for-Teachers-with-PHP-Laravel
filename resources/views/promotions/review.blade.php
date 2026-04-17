<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('promotions.index', ['school_year_id' => $schoolYear->id]) }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-[#333333]">
                Promotion Review — {{ config('school.grade_levels')[$gradeLevel] ?? $gradeLevel }} ({{ $schoolYear->name }})
            </h2>
        </div>
    </x-slot>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <ul class="list-disc list-inside text-sm text-red-600">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(empty($gradeLevelCandidates))
        <div class="bg-white rounded-lg shadow-sm text-center py-12">
            <p class="text-sm text-[#666666]">No students found for this grade level.</p>
        </div>
    @else
        <form method="POST" action="{{ route('promotions.process') }}" x-data="{ confirmSubmit: false }" x-on:submit.prevent="if(confirm('Process promotions for all listed students? This action cannot be undone.')) $el.submit()">
            @csrf
            <input type="hidden" name="from_school_year_id" value="{{ $schoolYear->id }}">

            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="flex items-center gap-4">
                    <label class="text-sm font-medium text-[#333333]">Promote to School Year:</label>
                    <select name="to_school_year_id" required class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                        <option value="">Select Target School Year</option>
                        @foreach($nextSchoolYears as $sy)
                            <option value="{{ $sy->id }}">{{ $sy->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Student Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">LRN</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">General Avg</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Recommendation</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider" style="min-width: 140px;">Decision</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider" style="min-width: 180px;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($gradeLevelCandidates as $index => $candidate)
                                @if($candidate['promotion'])
                                    {{-- Already processed --}}
                                    <tr class="bg-gray-50/50">
                                        <td class="px-4 py-3 text-sm text-[#666666]">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm text-[#666666]">{{ $candidate['student']->full_name }}</td>
                                        <td class="px-4 py-3 text-sm text-[#666666]">{{ $candidate['student']->lrn }}</td>
                                        <td class="px-4 py-3 text-center text-sm text-[#666666]">
                                            {{ $candidate['general_average'] !== null ? number_format($candidate['general_average'], 2) : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-center" colspan="3">
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                                Already {{ ucfirst($candidate['promotion']->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="{{ $candidate['general_average'] !== null && $candidate['general_average'] < 75 ? 'bg-red-50/30' : '' }}">
                                        <td class="px-4 py-3 text-sm text-[#666666]">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <span class="text-sm font-medium text-[#333333]">{{ $candidate['student']->full_name }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-[#666666]">{{ $candidate['student']->lrn }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($candidate['general_average'] !== null)
                                                <span class="text-sm font-medium {{ $candidate['general_average'] >= 75 ? 'text-green-700' : 'text-red-700' }}">
                                                    {{ number_format($candidate['general_average'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-xs text-yellow-700">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($candidate['recommendation'] === 'promoted')
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Promote</span>
                                            @elseif($candidate['recommendation'] === 'graduated')
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Graduate</span>
                                            @elseif($candidate['recommendation'] === 'retained')
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Retain</span>
                                            @else
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="hidden" name="decisions[{{ $index }}][student_id]" value="{{ $candidate['student']->id }}">
                                            <select name="decisions[{{ $index }}][status]" required class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-xs">
                                                <option value="promoted" {{ $candidate['recommendation'] === 'promoted' ? 'selected' : '' }}>Promote</option>
                                                <option value="retained" {{ $candidate['recommendation'] === 'retained' ? 'selected' : '' }}>Retain</option>
                                                @if($gradeLevel === 'grade_6')
                                                    <option value="graduated" {{ $candidate['recommendation'] === 'graduated' ? 'selected' : '' }}>Graduate</option>
                                                @endif
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="decisions[{{ $index }}][remarks]" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-xs" placeholder="Optional remarks">
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('promotions.index', ['school_year_id' => $schoolYear->id]) }}" class="text-sm text-[#666666] hover:text-[#333333]">Back</a>
                <x-primary-button>Process Promotions</x-primary-button>
            </div>
        </form>
    @endif
</x-app-layout>
