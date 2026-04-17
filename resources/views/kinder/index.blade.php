<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-[#333333]">Kindergarten Assessments</h2>
    </x-slot>

    <!-- Info Banner -->
    <div class="bg-secondary/50 border border-secondary-dark rounded-lg p-4 mb-6">
        <p class="text-sm text-[#333333]">
            Kindergarten uses qualitative ratings across 5 developmental domains: <strong>Beginning</strong>, <strong>Developing</strong>, and <strong>Proficient</strong>. Select a quarter to enter assessments.
        </p>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($students->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Student Name</th>
                        @foreach(config('school.quarters') as $quarter)
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">{{ $quarter }}</th>
                        @endforeach
                        <th class="px-6 py-3 text-right text-xs font-medium text-[#666666] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($students as $index => $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm text-[#666666]">{{ $index + 1 }}</td>
                            <td class="px-6 py-3 text-sm font-medium text-[#333333]">{{ $student->full_name }}</td>
                            @foreach(config('school.quarters') as $quarter)
                                @php
                                    $status = $assessmentStatuses[$student->id][$quarter] ?? null;
                                    $statusConfig = [
                                        null => ['label' => '—', 'class' => 'text-gray-300'],
                                        'draft' => ['label' => 'Draft', 'class' => 'bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-[10px] font-medium'],
                                        'submitted' => ['label' => 'Submitted', 'class' => 'bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-medium'],
                                        'approved' => ['label' => 'Approved', 'class' => 'bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[10px] font-medium'],
                                        'locked' => ['label' => 'Locked', 'class' => 'bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[10px] font-medium'],
                                    ];
                                    $config = $statusConfig[$status];
                                @endphp
                                <td class="px-6 py-3 text-center">
                                    <span class="{{ $config['class'] }}">{{ $config['label'] }}</span>
                                </td>
                            @endforeach
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('kinder-assessments.show', $student) }}" class="text-sm text-primary hover:text-primary-dark font-medium">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-6 py-12 text-center">
                <p class="text-[#666666]">No kindergarten students found.</p>
            </div>
        @endif
    </div>

    <!-- Quarter Entry Buttons -->
    <div class="mt-6 grid grid-cols-4 gap-4">
        @foreach(config('school.quarters') as $quarter)
            <a href="{{ route('kinder-assessments.create', ['quarter' => $quarter]) }}" class="bg-white rounded-lg shadow-sm p-4 text-center hover:bg-primary/5 transition border border-gray-200 hover:border-primary">
                <p class="text-lg font-semibold text-primary">{{ $quarter }}</p>
                <p class="text-xs text-[#666666] mt-1">Enter Assessments</p>
            </a>
        @endforeach
    </div>
</x-app-layout>
