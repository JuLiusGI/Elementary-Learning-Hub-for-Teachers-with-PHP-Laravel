<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-[#333333]">Reports & Forms</h2>
    </x-slot>

    <div x-data="{
        gradeLevel: '{{ $defaultGradeLevel }}',
        students: {{ Js::from($students) }},
        sf9Student: '',
        sf10Student: '',
        get filteredStudents() {
            return this.students[this.gradeLevel] || [];
        }
    }">
        <!-- Official DepEd Forms -->
        <h3 class="text-lg font-semibold text-[#333333] mb-4">Official DepEd Forms</h3>
        <div class="grid grid-cols-2 gap-6 mb-8">
            <!-- SF9 Report Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-primary/10 rounded-lg">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-[#333333]">SF9 — Report Card</h4>
                        <p class="text-xs text-[#666666]">Quarterly grades, attendance summary, and remarks</p>
                    </div>
                </div>

                @if(auth()->user()->isHeadTeacher())
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-[#666666] mb-1">Grade Level</label>
                        <select x-model="gradeLevel" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                            @foreach(config('school.grade_levels') as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-xs font-medium text-[#666666] mb-1">Student</label>
                    <select x-model="sf9Student" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                        <option value="">Select a student...</option>
                        <template x-for="student in filteredStudents" :key="student.id">
                            <option :value="student.id" x-text="student.name"></option>
                        </template>
                    </select>
                </div>

                <div class="flex gap-2">
                    <a :href="sf9Student ? '{{ url('reports/sf9') }}/' + sf9Student : '#'"
                       :class="sf9Student ? 'bg-primary text-white hover:bg-primary/90' : 'bg-gray-200 text-gray-400 cursor-not-allowed pointer-events-none'"
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Generate for Student
                    </a>
                    <a :href="'{{ route('reports.sf9-bulk') }}?grade_level=' + gradeLevel"
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-[#666666] hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        Bulk Generate
                    </a>
                </div>
            </div>

            <!-- SF10 Permanent Record -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-accent/10 rounded-lg">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-[#333333]">SF10 — Permanent Record</h4>
                        <p class="text-xs text-[#666666]">Learner's permanent academic record (Form 137)</p>
                    </div>
                </div>

                @if(auth()->user()->isHeadTeacher())
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-[#666666] mb-1">Grade Level</label>
                        <select x-model="gradeLevel" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                            @foreach(config('school.grade_levels') as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-xs font-medium text-[#666666] mb-1">Student</label>
                    <select x-model="sf10Student" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                        <option value="">Select a student...</option>
                        <template x-for="student in filteredStudents" :key="student.id">
                            <option :value="student.id" x-text="student.name"></option>
                        </template>
                    </select>
                </div>

                <a :href="sf10Student ? '{{ url('reports/sf10') }}/' + sf10Student : '#'"
                   :class="sf10Student ? 'bg-primary text-white hover:bg-primary/90' : 'bg-gray-200 text-gray-400 cursor-not-allowed pointer-events-none'"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Generate SF10
                </a>
            </div>
        </div>

        <!-- Summary Reports -->
        <h3 class="text-lg font-semibold text-[#333333] mb-4">Summary Reports</h3>
        <div class="grid grid-cols-2 gap-6">
            <!-- Monthly Attendance -->
            <div class="bg-white rounded-lg shadow-sm p-6" x-data="{
                attGradeLevel: '{{ $defaultGradeLevel }}',
                attYear: {{ now()->year }},
                attMonth: {{ now()->month }}
            }">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-secondary/50 rounded-lg">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-[#333333]">Monthly Attendance Report</h4>
                        <p class="text-xs text-[#666666]">Attendance summary per student for a month</p>
                    </div>
                </div>

                @if(auth()->user()->isHeadTeacher())
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-[#666666] mb-1">Grade Level</label>
                        <select x-model="attGradeLevel" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                            @foreach(config('school.grade_levels') as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-[#666666] mb-1">Year</label>
                        <select x-model="attYear" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                            @for($y = now()->year; $y >= now()->year - 2; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#666666] mb-1">Month</label>
                        <select x-model="attMonth" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                            @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $i => $name)
                                <option value="{{ $i + 1 }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <a :href="'{{ route('reports.attendance') }}?grade_level=' + attGradeLevel + '&year=' + attYear + '&month=' + attMonth"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Generate PDF
                </a>
            </div>

            <!-- Grade Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6" x-data="{
                gsGradeLevel: '{{ $defaultGradeLevel }}',
                gsQuarter: 'Q1'
            }">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-accent/10 rounded-lg">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-[#333333]">Grade Summary Report</h4>
                        <p class="text-xs text-[#666666]">All students' quarterly grades per subject</p>
                    </div>
                </div>

                @if(auth()->user()->isHeadTeacher())
                    <div class="mb-3">
                        <label class="block text-xs font-medium text-[#666666] mb-1">Grade Level</label>
                        <select x-model="gsGradeLevel" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                            @foreach(config('school.grade_levels') as $key => $label)
                                @if($key !== 'kinder')
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-xs font-medium text-[#666666] mb-1">Quarter</label>
                    <select x-model="gsQuarter" class="w-full border-gray-300 rounded-md text-sm focus:ring-primary focus:border-primary">
                        @foreach(config('school.quarters') as $q)
                            <option value="{{ $q }}">{{ $q }}</option>
                        @endforeach
                    </select>
                </div>

                <a :href="'{{ route('reports.grade-summary') }}?grade_level=' + gsGradeLevel + '&quarter=' + gsQuarter"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Generate PDF
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
