<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-[#333333]">Learning Materials</h2>
            <a href="{{ route('learning-materials.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                Upload Material
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('learning-materials.index') }}" class="flex items-center gap-4 flex-wrap">
            @if(auth()->user()->isHeadTeacher())
                <select name="grade_level" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                    <option value="">All Grade Levels</option>
                    @foreach(config('school.grade_levels') as $key => $label)
                        <option value="{{ $key }}" {{ request('grade_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            @endif
            <select name="subject_id" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                @endforeach
            </select>
            <select name="quarter" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                <option value="">All Quarters</option>
                @foreach(config('school.quarters') as $q)
                    <option value="{{ $q }}" {{ request('quarter') == $q ? 'selected' : '' }}>{{ $q }}</option>
                @endforeach
            </select>
            <select name="week_number" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                <option value="">All Weeks</option>
                @for($w = 1; $w <= 10; $w++)
                    <option value="{{ $w }}" {{ request('week_number') == $w ? 'selected' : '' }}>Week {{ $w }}</option>
                @endfor
            </select>
            <select name="file_type" class="border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                <option value="">All Types</option>
                <option value="pdf" {{ request('file_type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                <option value="image" {{ request('file_type') == 'image' ? 'selected' : '' }}>Image</option>
                <option value="video" {{ request('file_type') == 'video' ? 'selected' : '' }}>Video</option>
                <option value="link" {{ request('file_type') == 'link' ? 'selected' : '' }}>Link</option>
            </select>
            <x-primary-button>Filter</x-primary-button>
            @if(request()->hasAny(['subject_id', 'quarter', 'week_number', 'file_type', 'grade_level']))
                <a href="{{ route('learning-materials.index') }}" class="text-sm text-[#666666] hover:text-[#333333]">Clear</a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if($materials->isEmpty())
        <div class="bg-white rounded-lg shadow-sm text-center py-12">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path></svg>
            <p class="text-sm text-[#666666]">No materials found.</p>
            <a href="{{ route('learning-materials.create') }}" class="text-sm text-primary hover:underline mt-1 inline-block">Upload your first material</a>
        </div>
    @else
        <!-- Materials Grid -->
        <div class="grid grid-cols-2 gap-4">
            @foreach($materials as $material)
                <div class="bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition">
                    <div class="flex items-start gap-4">
                        <!-- File Type Icon -->
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center {{ match($material->file_type) {
                            'pdf' => 'bg-red-100 text-red-600',
                            'image' => 'bg-green-100 text-green-600',
                            'video' => 'bg-purple-100 text-purple-600',
                            'link' => 'bg-blue-100 text-blue-600',
                        } }}">
                            @if($material->file_type === 'pdf')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            @elseif($material->file_type === 'image')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @elseif($material->file_type === 'video')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <a href="{{ route('learning-materials.show', $material) }}" class="text-sm font-medium text-[#333333] hover:text-primary truncate block">{{ $material->title }}</a>
                            <p class="text-xs text-[#666666] mt-0.5">{{ $material->subject->name }} &bull; {{ $material->quarter }}{{ $material->week_number ? ' &bull; Week ' . $material->week_number : '' }}</p>
                            <div class="flex items-center gap-3 mt-2 text-xs text-[#666666]">
                                <span>{{ $material->uploader->name }}</span>
                                <span>{{ $material->created_at->format('M d, Y') }}</span>
                                @if($material->isFile())
                                    <span>{{ $material->formattedFileSize() }}</span>
                                @endif
                                @if($material->download_count > 0)
                                    <span class="px-1.5 py-0.5 bg-gray-100 rounded text-[10px]">{{ $material->download_count }} downloads</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-1 flex-shrink-0">
                            @if($material->isFile() && $material->is_downloadable)
                                <a href="{{ route('learning-materials.download', $material) }}" class="p-1.5 text-[#666666] hover:text-primary rounded hover:bg-gray-100" title="Download">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                                {{-- Save for Offline --}}
                                <button
                                    x-data="{ downloaded: isMaterialDownloaded({{ $material->id }}), downloading: false }"
                                    @click.prevent="
                                        if (downloaded || downloading) return;
                                        downloading = true;
                                        try {
                                            await downloadMaterialForOffline({{ $material->id }}, '{{ route('learning-materials.download', $material) }}', '{{ addslashes($material->title) }}');
                                            downloaded = true;
                                        } catch(e) { alert('Download failed: ' + e.message); }
                                        downloading = false;
                                    "
                                    :class="downloaded ? 'text-green-600' : 'text-[#666666] hover:text-primary'"
                                    class="p-1.5 rounded hover:bg-gray-100"
                                    :title="downloaded ? 'Available offline' : 'Save for offline'"
                                >
                                    <svg x-show="!downloading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path x-show="!downloaded" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                        <path x-show="downloaded" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <svg x-show="downloading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </button>
                            @endif
                            @can('update', $material)
                                <a href="{{ route('learning-materials.edit', $material) }}" class="p-1.5 text-[#666666] hover:text-[#333333] rounded hover:bg-gray-100" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                            @endcan
                            @can('delete', $material)
                                <form method="POST" action="{{ route('learning-materials.destroy', $material) }}" x-data x-on:submit.prevent="if(confirm('Delete this material?')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 rounded hover:bg-gray-100" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $materials->links() }}
        </div>
    @endif
</x-app-layout>
