<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-[#333333]">
            Dashboard &mdash; Administration
        </h2>
    </x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-4 gap-6">
        <!-- Total Students -->
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

        <!-- Total Teachers -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-primary-light">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[#666666]">Active Teachers</p>
                    <p class="text-3xl font-bold text-[#333333] mt-1">{{ $teacherCount }}</p>
                </div>
                <div class="p-3 bg-primary/10 rounded-full">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-accent">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[#666666]">Pending Approvals</p>
                    <p class="text-3xl font-bold text-[#333333] mt-1">{{ $pendingApprovalsCount }}</p>
                </div>
                <div class="p-3 bg-accent/10 rounded-full">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- School Year -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-secondary-dark">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[#666666]">School Year</p>
                    <p class="text-2xl font-bold text-[#333333] mt-1">{{ $currentSchoolYear->name ?? 'N/A' }}</p>
                </div>
                <div class="p-3 bg-secondary/50 rounded-full">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout: Attendance Overview + Pending Approvals -->
    <div class="mt-8 grid grid-cols-2 gap-6">
        <!-- Today's Attendance Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-[#333333] mb-4">Today's Attendance Overview</h3>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-700">{{ $attendanceOverview['present'] }}</p>
                    <p class="text-xs text-green-600 mt-1">Present</p>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-700">{{ $attendanceOverview['absent'] }}</p>
                    <p class="text-xs text-red-600 mt-1">Absent</p>
                </div>
                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-700">{{ $attendanceOverview['late'] }}</p>
                    <p class="text-xs text-yellow-600 mt-1">Late</p>
                </div>
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-700">{{ $attendanceOverview['excused'] }}</p>
                    <p class="text-xs text-blue-600 mt-1">Excused</p>
                </div>
            </div>
            <div class="pt-3 border-t border-gray-100">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-[#666666]">Recorded</span>
                    <span class="font-medium text-[#333333]">{{ $attendanceOverview['recorded'] }} / {{ $attendanceOverview['total_students'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-primary h-2.5 rounded-full" style="width: {{ $attendanceOverview['total_students'] > 0 ? round(($attendanceOverview['recorded'] / $attendanceOverview['total_students']) * 100) : 0 }}%"></div>
                </div>
                @if($attendanceOverview['not_recorded'] > 0)
                    <p class="text-xs text-accent mt-2">{{ $attendanceOverview['not_recorded'] }} students not yet recorded</p>
                @else
                    <p class="text-xs text-green-600 mt-2">All students recorded</p>
                @endif
            </div>
        </div>

        <!-- Pending Approvals List -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-[#333333]">Pending Approvals</h3>
                <a href="{{ route('approvals.index') }}" class="text-sm text-primary hover:underline">View All</a>
            </div>
            @if($allPendingApprovals->isNotEmpty())
                <div class="space-y-2">
                    @foreach($allPendingApprovals->take(5) as $approval)
                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition">
                            <div>
                                <p class="text-sm font-medium text-[#333333]">{{ $approval['grade_level_label'] }} &mdash; {{ $approval['subject'] }}</p>
                                <p class="text-xs text-[#666666]">{{ $approval['quarter'] }} &middot; {{ $approval['count'] }} record{{ $approval['count'] > 1 ? 's' : '' }}</p>
                            </div>
                            @if($approval['grade_level'] !== 'kinder')
                                <a href="{{ route('approvals.show', [$approval['grade_level'], $approval['subject_id'], $approval['quarter']]) }}" class="px-3 py-1.5 text-xs font-medium bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                                    Review
                                </a>
                            @else
                                <span class="px-3 py-1.5 text-xs font-medium bg-gray-100 text-[#666666] rounded-lg">Kinder</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <svg class="w-12 h-12 text-green-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm text-[#666666]">No pending approvals</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Announcement Composer Shortcut -->
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm p-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-primary/10 rounded-full">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-[#333333]">Announcements</h3>
                    <p class="text-xs text-[#666666]">{{ $activeAnnouncementsCount }} active announcement{{ $activeAnnouncementsCount !== 1 ? 's' : '' }}</p>
                </div>
            </div>
            <a href="{{ route('announcements.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary-dark transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Post New Announcement
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-[#333333] mb-4">Quick Actions</h3>
        <div class="grid grid-cols-3 gap-4">
            <a href="{{ route('students.index') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span class="text-sm font-medium">View Students</span>
            </a>
            <a href="{{ route('attendance.index') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span class="text-sm font-medium">View Attendance</span>
            </a>
            <a href="{{ route('approvals.index') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-medium">Review Approvals</span>
            </a>
            <a href="{{ route('grades.index') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="text-sm font-medium">View Grades</span>
            </a>
            <a href="{{ route('reports.index') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="text-sm font-medium">Generate Reports</span>
            </a>
            <a href="{{ route('teachers.index') }}" class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition flex items-center space-x-3 text-[#333333] hover:text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="text-sm font-medium">Manage Teachers</span>
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

    <!-- Grade Submission Status per Teacher -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-[#333333] mb-4">Grade Submission Status</h3>
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Grade Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Teacher</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Students</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Draft</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Approved</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Locked</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($gradeSubmissionStatus as $status)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-[#333333]">{{ $status['label'] }}</td>
                            <td class="px-6 py-4 text-sm text-[#666666]">{{ $status['teacher'] }}</td>
                            <td class="px-6 py-4 text-sm text-[#666666] text-center">{{ $status['students'] }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($status['draft'] > 0)
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700">{{ $status['draft'] }}</span>
                                @else
                                    <span class="text-xs text-gray-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($status['submitted'] > 0)
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">{{ $status['submitted'] }}</span>
                                @else
                                    <span class="text-xs text-gray-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($status['approved'] > 0)
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">{{ $status['approved'] }}</span>
                                @else
                                    <span class="text-xs text-gray-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($status['locked'] > 0)
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">{{ $status['locked'] }}</span>
                                @else
                                    <span class="text-xs text-gray-400">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
