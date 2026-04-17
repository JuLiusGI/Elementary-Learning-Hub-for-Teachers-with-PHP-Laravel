<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-[#333333]">
            Dashboard &mdash; {{ $gradeLevel }}
        </h2>
    </x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-4 gap-6">
        <!-- Student Count -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[#666666]">Total Students</p>
                    <p class="text-3xl font-bold text-[#333333] mt-1">{{ $studentCount }}</p>
                </div>
                <div class="p-3 bg-primary/10 rounded-full">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Today's Attendance -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-secondary-dark">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[#666666]">Today's Attendance</p>
                    <p class="text-3xl font-bold text-[#333333] mt-1">{{ $attendanceTodayCount }}<span class="text-lg text-[#666666]">/{{ $totalStudents }}</span></p>
                </div>
                <div class="p-3 bg-secondary/50 rounded-full">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
            </div>
        </div>

        <!-- Draft Grades -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-accent">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[#666666]">Draft Grades</p>
                    <p class="text-3xl font-bold text-[#333333] mt-1">{{ $draftGradesCount }}</p>
                </div>
                <div class="p-3 bg-accent/10 rounded-full">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Assignments -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[#666666]">Assignments</p>
                    <p class="text-3xl font-bold text-[#333333] mt-1">{{ $assignmentCount }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout: Attendance Breakdown + Pending Tasks -->
    <div class="mt-8 grid grid-cols-2 gap-6">
        <!-- Today's Attendance Breakdown -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-[#333333] mb-4">Today's Attendance Breakdown</h3>
            @if($attendanceRecorded)
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <span class="text-sm text-[#333333]">Present</span>
                        </div>
                        <span class="text-sm font-semibold text-[#333333]">{{ $attendanceBreakdown['present'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-500"></span>
                            <span class="text-sm text-[#333333]">Absent</span>
                        </div>
                        <span class="text-sm font-semibold text-[#333333]">{{ $attendanceBreakdown['absent'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                            <span class="text-sm text-[#333333]">Late</span>
                        </div>
                        <span class="text-sm font-semibold text-[#333333]">{{ $attendanceBreakdown['late'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <span class="text-sm text-[#333333]">Excused</span>
                        </div>
                        <span class="text-sm font-semibold text-[#333333]">{{ $attendanceBreakdown['excused'] }}</span>
                    </div>
                </div>
                @if($totalStudents > 0)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ round(($attendanceBreakdown['present'] / $totalStudents) * 100) }}%"></div>
                        </div>
                        <p class="text-xs text-[#666666] mt-1">{{ round(($attendanceBreakdown['present'] / $totalStudents) * 100) }}% present today</p>
                    </div>
                @endif
            @else
                <div class="text-center py-6">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <p class="text-sm text-[#666666] mb-3">Attendance not yet recorded today</p>
                    <a href="{{ route('attendance.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-dark transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Take Attendance
                    </a>
                </div>
            @endif
        </div>

        <!-- Pending Grade Tasks -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-[#333333] mb-4">Pending Grade Tasks</h3>
            @if(count($pendingGradeTasks) > 0)
                <div class="space-y-2">
                    @foreach($pendingGradeTasks as $task)
                        <a href="{{ $task['route'] }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition group">
                            <div class="flex items-center gap-3">
                                <div class="p-1.5 bg-accent/10 rounded">
                                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </div>
                                <span class="text-sm text-[#333333]">{{ $task['label'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-accent/10 text-accent">{{ $task['missing'] }} students</span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <svg class="w-12 h-12 text-green-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm text-[#666666]">All caught up! No pending grade tasks.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Students at Risk -->
    @if($atRiskStudents->isNotEmpty())
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-accent">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                    <h3 class="text-lg font-semibold text-[#333333]">Students at Risk</h3>
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">{{ $atRiskStudents->count() }} student{{ $atRiskStudents->count() > 1 ? 's' : '' }}</span>
                </div>
                <p class="text-xs text-[#666666] mb-3">Students with 3 or more absences this month ({{ now()->format('F Y') }})</p>
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-[#666666] uppercase">Student</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-[#666666] uppercase">LRN</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-[#666666] uppercase">Absences</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-[#666666] uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($atRiskStudents as $student)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-[#333333]">{{ $student->full_name }}</td>
                                    <td class="px-4 py-3 text-sm text-[#666666]">{{ $student->lrn }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $student->absence_count >= 5 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $student->absence_count }} days
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('students.show', $student) }}" class="text-sm text-primary hover:underline">View Profile</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-[#333333] mb-4">Quick Actions</h3>
        <div class="grid grid-cols-3 gap-4">
            <a href="{{ route('students.create') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span class="text-sm font-medium">Add Student</span>
            </a>
            <a href="{{ route('attendance.create') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span class="text-sm font-medium">Take Attendance</span>
            </a>
            @if(auth()->user()->grade_level === 'kinder')
                <a href="{{ route('kinder-assessments.index') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span class="text-sm font-medium">Enter Assessments</span>
                </a>
            @else
                <a href="{{ route('grades.index') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span class="text-sm font-medium">Enter Grades</span>
                </a>
            @endif
            <a href="{{ route('assignments.create') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span class="text-sm font-medium">New Assignment</span>
            </a>
            <a href="{{ route('learning-materials.create') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                <span class="text-sm font-medium">Upload Material</span>
            </a>
            <a href="{{ route('reports.index') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="text-sm font-medium">Generate Reports</span>
            </a>
        </div>
    </div>

    <!-- Recent Announcements -->
    @if($announcements->isNotEmpty())
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-[#333333]">Recent Announcements</h3>
                <a href="{{ route('announcements.index') }}" class="text-sm text-primary hover:underline">View All</a>
            </div>
            <div class="space-y-3">
                @foreach($announcements as $announcement)
                    <a href="{{ route('announcements.show', $announcement) }}" class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition">
                        <div class="flex items-center gap-2 mb-1">
                            @if($announcement->priority === 'urgent')
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Urgent</span>
                            @elseif($announcement->priority === 'important')
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Important</span>
                            @endif
                            @if(!$announcement->isReadBy(auth()->user()))
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-primary/10 text-primary">New</span>
                            @endif
                        </div>
                        <p class="text-sm font-medium text-[#333333]">{{ $announcement->title }}</p>
                        <p class="text-xs text-[#666666] mt-1">{{ $announcement->published_at?->format('M d, Y') }} &mdash; {{ Str::limit(strip_tags($announcement->content), 100) }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</x-app-layout>
