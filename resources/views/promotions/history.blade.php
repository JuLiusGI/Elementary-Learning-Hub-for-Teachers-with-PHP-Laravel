<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('promotions.index') }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-[#333333]">Promotion History</h2>
        </div>
    </x-slot>

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('promotions.history') }}" class="flex items-center gap-4">
            <label class="text-sm font-medium text-[#333333]">School Year:</label>
            <select name="school_year_id" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                <option value="">All School Years</option>
                @foreach($schoolYears as $sy)
                    <option value="{{ $sy->id }}" {{ request('school_year_id') == $sy->id ? 'selected' : '' }}>{{ $sy->name }}</option>
                @endforeach
            </select>
            <x-primary-button>Filter</x-primary-button>
            @if(request('school_year_id'))
                <a href="{{ route('promotions.history') }}" class="text-sm text-[#666666] hover:text-[#333333]">Clear</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($promotions->isEmpty())
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <p class="text-sm text-[#666666]">No promotion records found.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">From</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">To</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Average</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Decided By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($promotions as $promotion)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-[#333333]">{{ $promotion->student->full_name }}</p>
                                        <p class="text-xs text-[#666666]">{{ $promotion->fromSchoolYear->name }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-center text-sm text-[#666666]">
                                    {{ config('school.grade_levels')[$promotion->from_grade_level] ?? $promotion->from_grade_level }}
                                </td>
                                <td class="px-6 py-3 text-center text-sm text-[#666666]">
                                    {{ $promotion->to_grade_level === 'graduated' ? 'Graduated' : (config('school.grade_levels')[$promotion->to_grade_level] ?? $promotion->to_grade_level) }}
                                </td>
                                <td class="px-6 py-3 text-center text-sm">
                                    @if($promotion->general_average !== null)
                                        <span class="{{ $promotion->general_average >= 75 ? 'text-green-700' : 'text-red-700' }} font-medium">
                                            {{ number_format($promotion->general_average, 2) }}
                                        </span>
                                    @else
                                        <span class="text-[#666666]">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center">
                                    @if($promotion->status === 'promoted')
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Promoted</span>
                                    @elseif($promotion->status === 'graduated')
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Graduated</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Retained</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-[#666666]">{{ $promotion->decidedBy->name }}</td>
                                <td class="px-6 py-3 text-sm text-[#666666]">{{ $promotion->promoted_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($promotions->hasPages())
                <div class="px-6 py-3 border-t">
                    {{ $promotions->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
