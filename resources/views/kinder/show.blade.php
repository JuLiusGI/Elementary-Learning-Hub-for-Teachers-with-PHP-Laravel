<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('kinder-assessments.index') }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-[#333333]">Progress — {{ $student->full_name }}</h2>
                <p class="text-sm text-[#666666]">Kindergarten &bull; LRN: {{ $student->lrn }}</p>
            </div>
        </div>
    </x-slot>

    <!-- Progress Matrix -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Domain</th>
                    @foreach(config('school.quarters') as $quarter)
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">{{ $quarter }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($domains as $domainKey => $domainLabel)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-[#333333]">{{ $domainLabel }}</p>
                        </td>
                        @foreach(config('school.quarters') as $quarter)
                            @php
                                $assessment = ($assessments->get($quarter) ?? collect())->firstWhere('domain', $domainKey);
                                $ratingColors = [
                                    'beginning' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'developing' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'proficient' => 'bg-green-100 text-green-800 border-green-200',
                                ];
                            @endphp
                            <td class="px-6 py-4 text-center">
                                @if($assessment && $assessment->rating)
                                    <span class="px-3 py-1 text-xs font-medium rounded-full border {{ $ratingColors[$assessment->rating] }}">
                                        {{ config('school.kinder_ratings')[$assessment->rating] }}
                                    </span>
                                    @if($assessment->remarks)
                                        <p class="text-[10px] text-[#666666] mt-1">{{ $assessment->remarks }}</p>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-300">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Legend -->
    <div class="mt-4 bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center gap-6">
            <span class="text-xs text-[#666666]">Ratings:</span>
            <span class="flex items-center gap-1"><span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">Beginning</span><span class="text-xs text-[#666666]">— Needs support</span></span>
            <span class="flex items-center gap-1"><span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-blue-100 text-blue-800 border border-blue-200">Developing</span><span class="text-xs text-[#666666]">— Making progress</span></span>
            <span class="flex items-center gap-1"><span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-green-100 text-green-800 border border-green-200">Proficient</span><span class="text-xs text-[#666666]">— Meets expectations</span></span>
        </div>
    </div>

    <!-- Print Button -->
    <div class="mt-4 flex justify-end">
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-[#666666] hover:bg-gray-50 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print
        </button>
    </div>
</x-app-layout>
