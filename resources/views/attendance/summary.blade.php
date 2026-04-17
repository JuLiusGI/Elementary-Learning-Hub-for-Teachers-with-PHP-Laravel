<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.index') }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-[#333333]">Monthly Attendance Summary</h2>
        </div>
    </x-slot>

    <!-- Month Navigation -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex items-center justify-between">
            @php
                $prevMonth = \Carbon\Carbon::create($year, $month)->subMonth();
                $nextMonth = \Carbon\Carbon::create($year, $month)->addMonth();
            @endphp
            <a href="{{ route('attendance.summary', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="text-sm text-primary hover:text-primary-dark font-medium">
                &larr; {{ $prevMonth->format('F Y') }}
            </a>
            <h3 class="text-lg font-semibold text-[#333333]">{{ $monthName }}</h3>
            @if($nextMonth->lte(now()))
                <a href="{{ route('attendance.summary', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="text-sm text-primary hover:text-primary-dark font-medium">
                    {{ $nextMonth->format('F Y') }} &rarr;
                </a>
            @else
                <span></span>
            @endif
        </div>
    </div>

    <!-- Summary Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($summaryData->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Student Name</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">
                            <span class="text-green-600">Present</span>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">
                            <span class="text-red-600">Absent</span>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">
                            <span class="text-yellow-600">Late</span>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">
                            <span class="text-blue-600">Excused</span>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Total Days</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($summaryData as $index => $data)
                        @php
                            $rate = $data['total_days'] > 0 ? round(($data['present'] + $data['late']) / $data['total_days'] * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm text-[#666666]">{{ $index + 1 }}</td>
                            <td class="px-6 py-3">
                                <a href="{{ route('attendance.show', $data['student']) }}" class="text-sm font-medium text-primary hover:text-primary-dark">
                                    {{ $data['student']->full_name }}
                                </a>
                            </td>
                            <td class="px-6 py-3 text-center text-sm font-medium text-green-600">{{ $data['present'] }}</td>
                            <td class="px-6 py-3 text-center text-sm font-medium text-red-600">{{ $data['absent'] }}</td>
                            <td class="px-6 py-3 text-center text-sm font-medium text-yellow-600">{{ $data['late'] }}</td>
                            <td class="px-6 py-3 text-center text-sm font-medium text-blue-600">{{ $data['excused'] }}</td>
                            <td class="px-6 py-3 text-center text-sm text-[#333333]">{{ $data['total_days'] }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="text-sm font-medium {{ $rate >= 90 ? 'text-green-600' : ($rate >= 75 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $rate }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-6 py-12 text-center">
                <p class="text-[#666666]">No students found.</p>
            </div>
        @endif
    </div>

    <!-- Print Button -->
    <div class="mt-4 flex justify-end">
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-[#666666] hover:bg-gray-50 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print Summary
        </button>
    </div>
</x-app-layout>
