<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-[#333333]">Grades</h2>
        </div>
    </x-slot>

    <!-- Grade Level Filter (Head Teacher only) -->
    @if(auth()->user()->isHeadTeacher())
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('grades.index') }}" class="flex items-center gap-4">
                <div>
                    <x-input-label value="Grade Level" />
                    <select name="grade_level" onchange="this.form.submit()" class="mt-1 border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
                        @foreach(config('school.grade_levels') as $key => $label)
                            @if($key !== 'kinder')
                                <option value="{{ $key }}" {{ $gradeLevel === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    @endif

    <!-- Info Banner -->
    <div class="bg-secondary/50 border border-secondary-dark rounded-lg p-4 mb-6">
        <p class="text-sm text-[#333333]">
            <strong>{{ config('school.grade_levels')[$gradeLevel] ?? $gradeLevel }}</strong> — Select a subject and quarter to enter grades. Grade weights: WW (40%), PT (40%), QA (20%).
        </p>
    </div>

    <!-- Subjects Grid -->
    <div class="grid grid-cols-2 gap-6">
        @forelse($subjects as $subject)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h3 class="text-sm font-semibold text-[#333333]">{{ $subject->name }}</h3>
                    <p class="text-xs text-[#666666]">{{ $subject->code }}</p>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-4 gap-3">
                        @foreach(config('school.quarters') as $quarter)
                            @php
                                $status = $gradeStatuses[$subject->id][$quarter] ?? null;
                                $statusConfig = [
                                    null => ['label' => 'Not started', 'bg' => 'bg-gray-100 text-gray-500 hover:bg-gray-200', 'icon' => ''],
                                    'draft' => ['label' => 'Draft', 'bg' => 'bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100', 'icon' => ''],
                                    'submitted' => ['label' => 'Submitted', 'bg' => 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100', 'icon' => ''],
                                    'approved' => ['label' => 'Approved', 'bg' => 'bg-green-50 text-green-700 border border-green-200', 'icon' => ''],
                                    'locked' => ['label' => 'Locked', 'bg' => 'bg-gray-100 text-gray-500 border border-gray-200', 'icon' => ''],
                                ];
                                $config = $statusConfig[$status];
                            @endphp
                            @if($status === 'locked' || $status === 'approved')
                                <div class="rounded-lg p-3 text-center {{ $config['bg'] }}">
                                    <p class="text-xs font-semibold">{{ $quarter }}</p>
                                    <p class="text-[10px] mt-1">{{ $config['label'] }}</p>
                                </div>
                            @else
                                <a href="{{ route('grades.create', ['subject_id' => $subject->id, 'quarter' => $quarter, 'grade_level' => $gradeLevel]) }}" class="rounded-lg p-3 text-center transition {{ $config['bg'] }}">
                                    <p class="text-xs font-semibold">{{ $quarter }}</p>
                                    <p class="text-[10px] mt-1">{{ $config['label'] }}</p>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white rounded-lg shadow-sm px-6 py-12 text-center">
                <p class="text-[#666666]">No subjects found for this grade level.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
