<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-[#333333]">Attendance</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('attendance.summary', ['year' => now()->year, 'month' => now()->month]) }}" class="inline-flex items-center px-4 py-2 border border-primary text-primary text-sm font-medium rounded-md hover:bg-primary/5 transition">
                    Monthly Summary
                </a>
                <a href="{{ route('attendance.create', ['date' => $date]) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary-dark transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Take Attendance
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Date Selector & Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('attendance.index') }}" class="flex items-center gap-4">
            <div>
                <label class="block text-xs font-medium text-[#666666] mb-1">Date</label>
                <input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
            </div>

            @if(auth()->user()->isHeadTeacher())
                <div>
                    <label class="block text-xs font-medium text-[#666666] mb-1">Grade Level</label>
                    <select name="grade_level" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
                        <option value="">All Grade Levels</option>
                        @foreach(config('school.grade_levels') as $key => $label)
                            <option value="{{ $key }}" {{ request('grade_level') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="pt-5">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary-dark transition">
                    View
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    @if($summary['total'] > 0)
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                <p class="text-xs font-medium text-[#666666] uppercase">Present</p>
                <p class="text-2xl font-bold text-green-600">{{ $summary['present'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                <p class="text-xs font-medium text-[#666666] uppercase">Absent</p>
                <p class="text-2xl font-bold text-red-600">{{ $summary['absent'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                <p class="text-xs font-medium text-[#666666] uppercase">Late</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $summary['late'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                <p class="text-xs font-medium text-[#666666] uppercase">Excused</p>
                <p class="text-2xl font-bold text-blue-600">{{ $summary['excused'] }}</p>
            </div>
        </div>
    @endif

    <!-- Attendance Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($records->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Grade Level</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Time In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($records as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('attendance.show', $record->student) }}" class="text-sm font-medium text-primary hover:text-primary-dark">
                                    {{ $record->student->full_name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#666666]">{{ $record->student->grade_level_label }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'present' => 'bg-green-100 text-green-800',
                                        'absent' => 'bg-red-100 text-red-800',
                                        'late' => 'bg-yellow-100 text-yellow-800',
                                        'excused' => 'bg-blue-100 text-blue-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$record->status] }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#666666]">{{ $record->time_in ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-[#666666]">{{ $record->remarks ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <p class="mt-4 text-[#666666]">No attendance records for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}.</p>
                <a href="{{ route('attendance.create', ['date' => $date]) }}" class="mt-2 inline-flex items-center text-sm text-primary hover:text-primary-dark font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Take attendance for this date
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
