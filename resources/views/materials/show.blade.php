<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('learning-materials.index') }}" class="text-[#666666] hover:text-[#333333]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h2 class="text-xl font-semibold text-[#333333]">{{ $learningMaterial->title }}</h2>
                    <p class="text-sm text-[#666666]">{{ $learningMaterial->subject->name }} &bull; {{ $learningMaterial->quarter }}{{ $learningMaterial->week_number ? ' &bull; Week ' . $learningMaterial->week_number : '' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($learningMaterial->isFile() && $learningMaterial->is_downloadable)
                    <a href="{{ route('learning-materials.download', $learningMaterial) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download
                    </a>
                @endif
                @can('update', $learningMaterial)
                    <a href="{{ route('learning-materials.edit', $learningMaterial) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-[#666666] hover:bg-gray-50 transition">
                        Edit
                    </a>
                @endcan
                @can('delete', $learningMaterial)
                    <form method="POST" action="{{ route('learning-materials.destroy', $learningMaterial) }}" x-data x-on:submit.prevent="if(confirm('Delete this material?')) $el.submit()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 transition">
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Material Details -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-4 gap-6">
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider">Type</p>
                <span class="px-2 py-1 text-xs font-medium rounded-full mt-1 inline-block {{ match($learningMaterial->file_type) {
                    'pdf' => 'bg-red-100 text-red-700',
                    'image' => 'bg-green-100 text-green-700',
                    'video' => 'bg-purple-100 text-purple-700',
                    'link' => 'bg-blue-100 text-blue-700',
                } }}">{{ ucfirst($learningMaterial->file_type) }}</span>
            </div>
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider">Uploaded By</p>
                <p class="text-sm text-[#333333] mt-1">{{ $learningMaterial->uploader->name }}</p>
            </div>
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider">Upload Date</p>
                <p class="text-sm text-[#333333] mt-1">{{ $learningMaterial->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-[#666666] uppercase tracking-wider">Downloads</p>
                <p class="text-sm text-[#333333] mt-1">{{ $learningMaterial->download_count }}</p>
            </div>
        </div>
        @if($learningMaterial->isFile())
            <div class="mt-4 pt-4 border-t">
                <p class="text-xs text-[#666666] uppercase tracking-wider mb-1">File Info</p>
                <p class="text-sm text-[#333333]">{{ $learningMaterial->formattedFileSize() }} &bull; {{ $learningMaterial->mime_type }}</p>
            </div>
        @endif
        @if($learningMaterial->description)
            <div class="mt-4 pt-4 border-t">
                <p class="text-xs text-[#666666] uppercase tracking-wider mb-1">Description</p>
                <p class="text-sm text-[#333333]">{{ $learningMaterial->description }}</p>
            </div>
        @endif
    </div>

    <!-- Preview Area -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-sm font-semibold text-[#333333] mb-4">Preview</h3>

        @if($learningMaterial->file_type === 'image' && $learningMaterial->file_path)
            <div class="flex justify-center">
                <img src="{{ asset('storage/' . $learningMaterial->file_path) }}" alt="{{ $learningMaterial->title }}" class="max-w-full max-h-[500px] rounded-lg shadow-sm">
            </div>
        @elseif($learningMaterial->file_type === 'pdf' && $learningMaterial->file_path)
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                <p class="text-sm text-[#666666] mb-3">PDF Document</p>
                @if($learningMaterial->is_downloadable)
                    <a href="{{ route('learning-materials.download', $learningMaterial) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download PDF
                    </a>
                @endif
            </div>
        @elseif($learningMaterial->file_type === 'video' && $learningMaterial->external_url)
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-purple-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm text-[#666666] mb-3">Video Link</p>
                <a href="{{ $learningMaterial->external_url }}" target="_blank" rel="noopener" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    Open Video
                </a>
            </div>
        @elseif($learningMaterial->file_type === 'link' && $learningMaterial->external_url)
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-blue-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                <p class="text-sm text-[#666666] mb-3">External Link</p>
                <a href="{{ $learningMaterial->external_url }}" target="_blank" rel="noopener" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    Open Link
                </a>
                <p class="text-xs text-[#666666] mt-2 break-all">{{ $learningMaterial->external_url }}</p>
            </div>
        @endif
    </div>
</x-app-layout>
