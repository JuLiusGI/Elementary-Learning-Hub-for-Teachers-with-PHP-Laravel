<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('attendance.index') }}" class="text-[#666666] hover:text-[#333333]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="text-xl font-semibold text-[#333333]">Take Attendance</h2>
            </div>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('attendance.store') }}" x-data="{
        date: '{{ $date }}',
        submitting: false,
        students: @js($students->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->full_name,
            'status' => $existingRecords->has($s->id) ? $existingRecords[$s->id]->status : 'present',
            'time_in' => $existingRecords->has($s->id) ? ($existingRecords[$s->id]->time_in ?? '') : '',
            'remarks' => $existingRecords->has($s->id) ? ($existingRecords[$s->id]->remarks ?? '') : '',
        ])->values()),
        markAllPresent() {
            this.students.forEach(s => { s.status = 'present'; s.time_in = ''; });
        },
        markAllAbsent() {
            this.students.forEach(s => { s.status = 'absent'; s.time_in = ''; });
        },
        async submitForm() {
            this.submitting = true;
            const data = {
                date: this.date,
                attendance: this.students.map(s => ({
                    student_id: s.id,
                    status: s.status,
                    time_in: s.time_in || null,
                    remarks: s.remarks || null,
                })),
            };
            const saved = await this.$offlineSubmitAttendance(data);
            if (saved) {
                alert('Attendance saved offline. It will sync when you are back online.');
                window.location.href = '{{ route('attendance.index') }}';
            } else {
                this.submitting = false;
                this.$el.submit();
            }
        }
    }" @submit.prevent="submitForm()">
        @csrf

        <!-- Date Selector -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex items-center gap-4">
                <div>
                    <x-input-label value="Date" />
                    <input type="date" name="date" x-model="date" max="{{ now()->toDateString() }}" class="mt-1 border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm" required>
                    <x-input-error :messages="$errors->get('date')" class="mt-1" />
                </div>
                <div class="flex items-end gap-2 pt-5">
                    <button type="button" @click="markAllPresent()" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                        Mark All Present
                    </button>
                    <button type="button" @click="markAllAbsent()" class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-md hover:bg-red-200 transition">
                        Mark All Absent
                    </button>
                </div>
                <div class="ml-auto text-sm text-[#666666]">
                    <span x-text="students.length"></span> students
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-red-600">Please correct the errors below.</p>
            </div>
        @endif

        <!-- Attendance Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($students->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider w-8">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Student Name</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider w-32">Time In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider w-48">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(student, index) in students" :key="student.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-[#666666]" x-text="index + 1"></td>
                                <td class="px-6 py-3">
                                    <input type="hidden" :name="'attendance[' + index + '][student_id]'" :value="student.id">
                                    <span class="text-sm font-medium text-[#333333]" x-text="student.name"></span>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <label class="cursor-pointer">
                                            <input type="radio" :name="'attendance[' + index + '][status]'" value="present" x-model="student.status" class="sr-only peer">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full border peer-checked:bg-green-100 peer-checked:text-green-800 peer-checked:border-green-300 border-gray-200 text-gray-400">
                                                Present
                                            </span>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" :name="'attendance[' + index + '][status]'" value="absent" x-model="student.status" class="sr-only peer">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full border peer-checked:bg-red-100 peer-checked:text-red-800 peer-checked:border-red-300 border-gray-200 text-gray-400">
                                                Absent
                                            </span>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" :name="'attendance[' + index + '][status]'" value="late" x-model="student.status" class="sr-only peer">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full border peer-checked:bg-yellow-100 peer-checked:text-yellow-800 peer-checked:border-yellow-300 border-gray-200 text-gray-400">
                                                Late
                                            </span>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" :name="'attendance[' + index + '][status]'" value="excused" x-model="student.status" class="sr-only peer">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full border peer-checked:bg-blue-100 peer-checked:text-blue-800 peer-checked:border-blue-300 border-gray-200 text-gray-400">
                                                Excused
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <input type="time" :name="'attendance[' + index + '][time_in]'" x-model="student.time_in" x-show="student.status === 'late'" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
                                </td>
                                <td class="px-6 py-3">
                                    <input type="text" :name="'attendance[' + index + '][remarks]'" x-model="student.remarks" placeholder="Optional" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm text-sm">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <!-- Submit -->
                <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-end gap-3">
                    <a href="{{ route('attendance.index') }}" class="text-sm text-[#666666] hover:text-[#333333]">Cancel</a>
                    <x-primary-button>
                        Save Attendance
                    </x-primary-button>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <p class="text-[#666666]">No active students found for your class.</p>
                    <a href="{{ route('students.create') }}" class="mt-2 inline-flex items-center text-sm text-primary hover:text-primary-dark font-medium">Add students first</a>
                </div>
            @endif
        </div>
    </form>
</x-app-layout>
