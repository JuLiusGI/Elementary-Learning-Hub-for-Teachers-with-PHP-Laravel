<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('learning-materials.index') }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-[#333333]">Upload Material</h2>
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

    <form method="POST" action="{{ route('learning-materials.store') }}" enctype="multipart/form-data" x-data="{ fileType: '{{ old('file_type', 'pdf') }}' }">
        @csrf
        <div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label for="title" class="block text-sm font-medium text-[#333333] mb-1">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm" placeholder="e.g., Addition Worksheet - Week 1">
                </div>

                <div>
                    <label for="subject_id" class="block text-sm font-medium text-[#333333] mb-1">Subject</label>
                    <select name="subject_id" id="subject_id" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="quarter" class="block text-sm font-medium text-[#333333] mb-1">Quarter</label>
                    <select name="quarter" id="quarter" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                        @foreach(config('school.quarters') as $q)
                            <option value="{{ $q }}" {{ old('quarter') == $q ? 'selected' : '' }}>{{ $q }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="week_number" class="block text-sm font-medium text-[#333333] mb-1">Week <span class="text-[#666666] font-normal">(optional)</span></label>
                    <select name="week_number" id="week_number" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                        <option value="">Not specified</option>
                        @for($w = 1; $w <= 10; $w++)
                            <option value="{{ $w }}" {{ old('week_number') == $w ? 'selected' : '' }}>Week {{ $w }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="file_type" class="block text-sm font-medium text-[#333333] mb-1">Material Type</label>
                    <select name="file_type" id="file_type" required x-model="fileType" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                        <option value="pdf">PDF Document</option>
                        <option value="image">Image</option>
                        <option value="video">Video Link</option>
                        <option value="link">External Link</option>
                    </select>
                </div>

                <!-- File Upload (for PDF and Image) -->
                <div class="col-span-2" x-show="fileType === 'pdf' || fileType === 'image'" x-transition>
                    <label for="file" class="block text-sm font-medium text-[#333333] mb-1">File</label>
                    <input type="file" name="file" id="file" class="w-full border border-gray-300 rounded-md text-sm file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20"
                        :accept="fileType === 'pdf' ? '.pdf' : '.jpg,.jpeg,.png,.gif,.webp'">
                    <p class="text-xs text-[#666666] mt-1" x-show="fileType === 'pdf'">Accepted: PDF files up to 50MB</p>
                    <p class="text-xs text-[#666666] mt-1" x-show="fileType === 'image'">Accepted: JPG, PNG, GIF, WebP up to 10MB</p>
                </div>

                <!-- URL Input (for Video and Link) -->
                <div class="col-span-2" x-show="fileType === 'video' || fileType === 'link'" x-transition>
                    <label for="external_url" class="block text-sm font-medium text-[#333333] mb-1">URL</label>
                    <input type="url" name="external_url" id="external_url" value="{{ old('external_url') }}" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm"
                        :placeholder="fileType === 'video' ? 'https://youtube.com/watch?v=...' : 'https://example.com/resource'">
                </div>

                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-[#333333] mb-1">Description <span class="text-[#666666] font-normal">(optional)</span></label>
                    <textarea name="description" id="description" rows="3" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm" placeholder="Brief description of this material...">{{ old('description') }}</textarea>
                </div>

                <div class="col-span-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_downloadable" value="1" checked class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-sm text-[#333333]">Allow download</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('learning-materials.index') }}" class="text-sm text-[#666666] hover:text-[#333333]">Cancel</a>
                <x-primary-button>Upload Material</x-primary-button>
            </div>
        </div>
    </form>
</x-app-layout>
