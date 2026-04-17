<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('assignments.show', $assignment) }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-[#333333]">Edit Assignment</h2>
        </div>
    </x-slot>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <ul class="list-disc list-inside text-sm text-red-600">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('assignments.update', $assignment) }}">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label for="title" class="block text-sm font-medium text-[#333333] mb-1">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $assignment->title) }}" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                </div>

                <div>
                    <label for="subject_id" class="block text-sm font-medium text-[#333333] mb-1">Subject</label>
                    <select name="subject_id" id="subject_id" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $assignment->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-[#333333] mb-1">Type</label>
                    <select name="type" id="type" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                        <option value="written_work" {{ old('type', $assignment->type) == 'written_work' ? 'selected' : '' }}>Written Work (WW)</option>
                        <option value="performance_task" {{ old('type', $assignment->type) == 'performance_task' ? 'selected' : '' }}>Performance Task (PT)</option>
                    </select>
                </div>

                <div>
                    <label for="quarter" class="block text-sm font-medium text-[#333333] mb-1">Quarter</label>
                    <select name="quarter" id="quarter" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                        @foreach(config('school.quarters') as $q)
                            <option value="{{ $q }}" {{ old('quarter', $assignment->quarter) == $q ? 'selected' : '' }}>{{ $q }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="max_score" class="block text-sm font-medium text-[#333333] mb-1">Max Score</label>
                    <input type="number" name="max_score" id="max_score" value="{{ old('max_score', $assignment->max_score) }}" required step="0.01" min="1" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-[#333333] mb-1">Due Date <span class="text-[#666666] font-normal">(optional)</span></label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $assignment->due_date?->format('Y-m-d')) }}" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                </div>

                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-[#333333] mb-1">Description <span class="text-[#666666] font-normal">(optional)</span></label>
                    <textarea name="description" id="description" rows="3" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">{{ old('description', $assignment->description) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('assignments.show', $assignment) }}" class="text-sm text-[#666666] hover:text-[#333333]">Cancel</a>
                <x-primary-button>Update Assignment</x-primary-button>
            </div>
        </div>
    </form>
</x-app-layout>
