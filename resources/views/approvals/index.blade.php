<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-[#333333]">Grade Approvals</h2>
    </x-slot>

    <!-- Status Tabs -->
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="flex border-b">
            @foreach(['submitted' => 'Pending', 'approved' => 'Approved', 'locked' => 'Locked'] as $key => $label)
                <a href="{{ route('approvals.index', ['status' => $key]) }}" class="px-6 py-3 text-sm font-medium border-b-2 {{ $statusFilter === $key ? 'border-primary text-primary' : 'border-transparent text-[#666666] hover:text-[#333333]' }}">
                    {{ $label }}
                    @if($key === 'submitted')
                        @php $pendingCount = $submissions->where('status', 'submitted')->count(); @endphp
                        @if($pendingCount > 0)
                            <span class="ml-1 px-2 py-0.5 text-xs bg-accent text-white rounded-full">{{ $pendingCount }}</span>
                        @endif
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($submissions->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Grade Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Quarter</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Students</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#666666] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($submissions as $submission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-[#333333]">{{ $submission['grade_level_label'] }}</td>
                            <td class="px-6 py-4 text-sm text-[#666666]">{{ $submission['subject'] }}</td>
                            <td class="px-6 py-4 text-center text-sm font-medium text-[#333333]">{{ $submission['quarter'] }}</td>
                            <td class="px-6 py-4 text-center text-sm text-[#666666]">{{ $submission['student_count'] }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusBadge = match($submission['status']) {
                                        'submitted' => 'bg-blue-100 text-blue-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'locked' => 'bg-gray-100 text-gray-600',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusBadge }}">{{ ucfirst($submission['status']) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-[#666666]">
                                {{ $submission['submitted_at'] ? \Carbon\Carbon::parse($submission['submitted_at'])->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($submission['status'] === 'submitted')
                                    <a href="{{ route('approvals.show', [$submission['grade_level'], $submission['subject_id'] ?? 'kinder', $submission['quarter']]) }}" class="text-sm text-primary hover:text-primary-dark font-medium">
                                        Review
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="mt-4 text-[#666666]">No {{ $statusFilter }} submissions.</p>
            </div>
        @endif
    </div>
</x-app-layout>
