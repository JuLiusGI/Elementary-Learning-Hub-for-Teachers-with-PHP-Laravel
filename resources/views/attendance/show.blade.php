<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.index') }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-[#333333]">Attendance — {{ $student->full_name }}</h2>
        </div>
    </x-slot>

    <!-- Month Navigation -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex items-center justify-between">
            @php
                $prevMonth = \Carbon\Carbon::create($year, $month)->subMonth();
                $nextMonth = \Carbon\Carbon::create($year, $month)->addMonth();
            @endphp
            <a href="{{ route('attendance.show', [$student, 'year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="text-sm text-primary hover:text-primary-dark font-medium">
                &larr; {{ $prevMonth->format('F Y') }}
            </a>
            <h3 class="text-lg font-semibold text-[#333333]">{{ $monthName }}</h3>
            @if($nextMonth->lte(now()))
                <a href="{{ route('attendance.show', [$student, 'year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="text-sm text-primary hover:text-primary-dark font-medium">
                    {{ $nextMonth->format('F Y') }} &rarr;
                </a>
            @else
                <span></span>
            @endif
        </div>
    </div>

    <!-- Student Info -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                <span class="text-sm font-medium text-primary">{{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}</span>
            </div>
            <div>
                <p class="font-medium text-[#333333]">{{ $student->full_name }}</p>
                <p class="text-sm text-[#666666]">{{ $student->grade_level_label }} &bull; LRN: {{ $student->lrn }}</p>
            </div>
        </div>
    </div>

    <!-- Summary -->
    @php
        $presentCount = $records->where('status', 'present')->count();
        $absentCount = $records->where('status', 'absent')->count();
        $lateCount = $records->where('status', 'late')->count();
        $excusedCount = $records->where('status', 'excused')->count();
        $totalDays = $records->count();
        $rate = $totalDays > 0 ? round(($presentCount + $lateCount) / $totalDays * 100, 1) : 0;
    @endphp
    <div class="grid grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-3 text-center border-t-2 border-green-500">
            <p class="text-xs text-[#666666] uppercase">Present</p>
            <p class="text-xl font-bold text-green-600">{{ $presentCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-3 text-center border-t-2 border-red-500">
            <p class="text-xs text-[#666666] uppercase">Absent</p>
            <p class="text-xl font-bold text-red-600">{{ $absentCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-3 text-center border-t-2 border-yellow-500">
            <p class="text-xs text-[#666666] uppercase">Late</p>
            <p class="text-xl font-bold text-yellow-600">{{ $lateCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-3 text-center border-t-2 border-blue-500">
            <p class="text-xs text-[#666666] uppercase">Excused</p>
            <p class="text-xl font-bold text-blue-600">{{ $excusedCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-3 text-center border-t-2 border-primary">
            <p class="text-xs text-[#666666] uppercase">Rate</p>
            <p class="text-xl font-bold text-primary">{{ $rate }}%</p>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-7 gap-2">
            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                <div class="text-center text-xs font-medium text-[#666666] uppercase py-2">{{ $day }}</div>
            @endforeach

            @php
                $firstDay = \Carbon\Carbon::create($year, $month, 1);
                $startPad = ($firstDay->dayOfWeekIso - 1);
            @endphp

            @for($i = 0; $i < $startPad; $i++)
                <div></div>
            @endfor

            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateKey = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $record = $records->get($dateKey);
                    $isWeekend = \Carbon\Carbon::create($year, $month, $day)->isWeekend();
                    $bgClass = 'bg-gray-50 text-gray-400';
                    if ($record) {
                        $bgClass = match($record->status) {
                            'present' => 'bg-green-100 text-green-800 border border-green-200',
                            'absent' => 'bg-red-100 text-red-800 border border-red-200',
                            'late' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                            'excused' => 'bg-blue-100 text-blue-800 border border-blue-200',
                        };
                    } elseif ($isWeekend) {
                        $bgClass = 'bg-gray-100 text-gray-300';
                    }
                @endphp
                <div class="rounded-lg p-2 text-center {{ $bgClass }}" title="{{ $record ? ucfirst($record->status) : ($isWeekend ? 'Weekend' : 'No record') }}">
                    <span class="text-sm font-medium">{{ $day }}</span>
                    @if($record)
                        <p class="text-[10px] mt-0.5">{{ ucfirst(substr($record->status, 0, 1)) }}</p>
                    @endif
                </div>
            @endfor
        </div>

        <!-- Legend -->
        <div class="flex items-center gap-4 mt-6 pt-4 border-t">
            <span class="text-xs text-[#666666]">Legend:</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-100 border border-green-200"></span><span class="text-xs text-[#666666]">Present</span></span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-100 border border-red-200"></span><span class="text-xs text-[#666666]">Absent</span></span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-100 border border-yellow-200"></span><span class="text-xs text-[#666666]">Late</span></span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-100 border border-blue-200"></span><span class="text-xs text-[#666666]">Excused</span></span>
        </div>
    </div>
</x-app-layout>
