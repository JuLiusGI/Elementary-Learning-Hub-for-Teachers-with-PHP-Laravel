<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('grades.index') }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-[#333333]">{{ $subject->name }} — {{ $quarter }}</h2>
                <p class="text-sm text-[#666666]">{{ config('school.grade_levels')[$gradeLevel] ?? $gradeLevel }}</p>
            </div>
        </div>
    </x-slot>

    @if($isLocked)
        <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600">These grades are locked and cannot be edited.</p>
        </div>
    @elseif($isSubmitted)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-700">These grades have been submitted for approval and cannot be edited until reviewed.</p>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-red-600">Please correct the errors below.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('grades.store') }}" x-data="{
        submitting: false,
        grades: @js($students->map(fn($s) => [
            'student_id' => $s->id,
            'name' => $s->full_name,
            'ww_total' => $existingGrades->has($s->id) ? (float)($existingGrades[$s->id]->ww_total_score ?? 0) : null,
            'ww_max' => $existingGrades->has($s->id) ? (float)($existingGrades[$s->id]->ww_max_score ?? 0) : null,
            'pt_total' => $existingGrades->has($s->id) ? (float)($existingGrades[$s->id]->pt_total_score ?? 0) : null,
            'pt_max' => $existingGrades->has($s->id) ? (float)($existingGrades[$s->id]->pt_max_score ?? 0) : null,
            'qa_score' => $existingGrades->has($s->id) ? (float)($existingGrades[$s->id]->qa_score ?? 0) : null,
            'qa_max' => $existingGrades->has($s->id) ? (float)($existingGrades[$s->id]->qa_max_score ?? 0) : null,
            'quarterly_grade' => $existingGrades->has($s->id) ? (float)($existingGrades[$s->id]->quarterly_grade ?? 0) : 0,
            'remarks' => $existingGrades->has($s->id) ? ($existingGrades[$s->id]->remarks ?? '') : '',
        ])->values()),
        calculate(index) {
            let g = this.grades[index];
            let wwPct = g.ww_max > 0 ? (g.ww_total / g.ww_max) * 100 : 0;
            let ptPct = g.pt_max > 0 ? (g.pt_total / g.pt_max) * 100 : 0;
            let qaPct = g.qa_max > 0 ? (g.qa_score / g.qa_max) * 100 : 0;
            g.quarterly_grade = parseFloat((wwPct * 0.40 + ptPct * 0.40 + qaPct * 0.20).toFixed(2));
            g.remarks = g.quarterly_grade >= 75 ? 'Passed' : 'Failed';
        },
        async submitForm() {
            this.submitting = true;
            const data = {
                subject_id: {{ $subject->id }},
                quarter: '{{ $quarter }}',
                grades: this.grades.map(g => ({
                    student_id: g.student_id,
                    ww_total_score: g.ww_total,
                    ww_max_score: g.ww_max,
                    pt_total_score: g.pt_total,
                    pt_max_score: g.pt_max,
                    qa_score: g.qa_score,
                    qa_max_score: g.qa_max,
                })),
            };
            const saved = await this.$offlineSubmitGrades(data);
            if (saved) {
                alert('Grades saved offline. They will sync when you are back online.');
                window.location.href = '{{ route('grades.index') }}';
            } else {
                this.submitting = false;
                this.$el.submit();
            }
        }
    }" @submit.prevent="submitForm()">
        @csrf
        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
        <input type="hidden" name="quarter" value="{{ $quarter }}">

        <!-- Weight Legend -->
        <div class="bg-secondary/50 border border-secondary-dark rounded-lg p-3 mb-6">
            <div class="flex items-center gap-6 text-xs text-[#666666]">
                <span><strong class="text-[#333333]">WW</strong> Written Work (40%)</span>
                <span><strong class="text-[#333333]">PT</strong> Performance Task (40%)</span>
                <span><strong class="text-[#333333]">QA</strong> Quarterly Assessment (20%)</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider w-8">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider min-w-[180px]">Student</th>
                            <th colspan="2" class="px-4 py-3 text-center text-xs font-medium text-yellow-700 uppercase tracking-wider bg-yellow-50">WW (40%)</th>
                            <th colspan="2" class="px-4 py-3 text-center text-xs font-medium text-blue-700 uppercase tracking-wider bg-blue-50">PT (40%)</th>
                            <th colspan="2" class="px-4 py-3 text-center text-xs font-medium text-purple-700 uppercase tracking-wider bg-purple-50">QA (20%)</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Grade</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Remarks</th>
                        </tr>
                        <tr class="bg-gray-50 border-t">
                            <th></th>
                            <th></th>
                            <th class="px-4 py-1 text-center text-[10px] text-[#666666] bg-yellow-50">Score</th>
                            <th class="px-4 py-1 text-center text-[10px] text-[#666666] bg-yellow-50">Max</th>
                            <th class="px-4 py-1 text-center text-[10px] text-[#666666] bg-blue-50">Score</th>
                            <th class="px-4 py-1 text-center text-[10px] text-[#666666] bg-blue-50">Max</th>
                            <th class="px-4 py-1 text-center text-[10px] text-[#666666] bg-purple-50">Score</th>
                            <th class="px-4 py-1 text-center text-[10px] text-[#666666] bg-purple-50">Max</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(g, index) in grades" :key="g.student_id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-[#666666]" x-text="index + 1"></td>
                                <td class="px-4 py-2">
                                    <input type="hidden" :name="'grades[' + index + '][student_id]'" :value="g.student_id">
                                    <span class="text-sm font-medium text-[#333333]" x-text="g.name"></span>
                                </td>
                                <!-- WW -->
                                <td class="px-2 py-2 bg-yellow-50/30">
                                    <input type="number" step="0.01" min="0" :name="'grades[' + index + '][ww_total_score]'" x-model.number="g.ww_total" @input="calculate(index)" class="w-20 text-center border-gray-300 focus:border-primary focus:ring-primary rounded text-sm" :disabled="{{ $isLocked || $isSubmitted ? 'true' : 'false' }}">
                                </td>
                                <td class="px-2 py-2 bg-yellow-50/30">
                                    <input type="number" step="0.01" min="1" :name="'grades[' + index + '][ww_max_score]'" x-model.number="g.ww_max" @input="calculate(index)" class="w-20 text-center border-gray-300 focus:border-primary focus:ring-primary rounded text-sm" :disabled="{{ $isLocked || $isSubmitted ? 'true' : 'false' }}">
                                </td>
                                <!-- PT -->
                                <td class="px-2 py-2 bg-blue-50/30">
                                    <input type="number" step="0.01" min="0" :name="'grades[' + index + '][pt_total_score]'" x-model.number="g.pt_total" @input="calculate(index)" class="w-20 text-center border-gray-300 focus:border-primary focus:ring-primary rounded text-sm" :disabled="{{ $isLocked || $isSubmitted ? 'true' : 'false' }}">
                                </td>
                                <td class="px-2 py-2 bg-blue-50/30">
                                    <input type="number" step="0.01" min="1" :name="'grades[' + index + '][pt_max_score]'" x-model.number="g.pt_max" @input="calculate(index)" class="w-20 text-center border-gray-300 focus:border-primary focus:ring-primary rounded text-sm" :disabled="{{ $isLocked || $isSubmitted ? 'true' : 'false' }}">
                                </td>
                                <!-- QA -->
                                <td class="px-2 py-2 bg-purple-50/30">
                                    <input type="number" step="0.01" min="0" :name="'grades[' + index + '][qa_score]'" x-model.number="g.qa_score" @input="calculate(index)" class="w-20 text-center border-gray-300 focus:border-primary focus:ring-primary rounded text-sm" :disabled="{{ $isLocked || $isSubmitted ? 'true' : 'false' }}">
                                </td>
                                <td class="px-2 py-2 bg-purple-50/30">
                                    <input type="number" step="0.01" min="1" :name="'grades[' + index + '][qa_max_score]'" x-model.number="g.qa_max" @input="calculate(index)" class="w-20 text-center border-gray-300 focus:border-primary focus:ring-primary rounded text-sm" :disabled="{{ $isLocked || $isSubmitted ? 'true' : 'false' }}">
                                </td>
                                <!-- Computed Grade -->
                                <td class="px-4 py-2 text-center">
                                    <span class="text-sm font-bold" :class="g.quarterly_grade >= 75 ? 'text-green-600' : (g.quarterly_grade > 0 ? 'text-red-600' : 'text-gray-400')" x-text="g.quarterly_grade > 0 ? g.quarterly_grade.toFixed(2) : '—'"></span>
                                </td>
                                <!-- Remarks -->
                                <td class="px-4 py-2 text-center">
                                    <span class="text-xs font-medium" :class="g.remarks === 'Passed' ? 'text-green-600' : (g.remarks === 'Failed' ? 'text-red-600' : 'text-gray-400')" x-text="g.remarks || '—'"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            @if(!$isLocked && !$isSubmitted)
                <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
                    <a href="{{ route('grades.index') }}" class="text-sm text-[#666666] hover:text-[#333333]">Cancel</a>
                    <div class="flex items-center gap-3">
                        <x-primary-button>
                            Save as Draft
                        </x-primary-button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Submit for Approval (separate form below the grade entry) -->
        @if(!$isLocked && !$isSubmitted && $existingGrades->isNotEmpty())
            <div class="mt-4 flex justify-end">
                <form method="POST" action="{{ route('grades.submit') }}" x-data x-on:submit.prevent="if(confirm('Submit these grades for Head Teacher approval? You will not be able to edit them until reviewed.')) $el.submit()">
                    @csrf
                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                    <input type="hidden" name="quarter" value="{{ $quarter }}">
                    <input type="hidden" name="grade_level" value="{{ $gradeLevel }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Submit for Approval
                    </button>
                </form>
            </div>
        @endif
    </form>
</x-app-layout>
