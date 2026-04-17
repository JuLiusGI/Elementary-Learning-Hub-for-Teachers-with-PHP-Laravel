<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('announcements.index') }}" class="text-[#666666] hover:text-[#333333]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="text-xl font-semibold text-[#333333]">Announcement</h2>
            </div>
            @can('update', $announcement)
                <div class="flex items-center gap-3">
                    <a href="{{ route('announcements.edit', $announcement) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-sm font-medium rounded-md text-[#333333] hover:bg-gray-50 transition">Edit</a>
                    <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" x-data x-on:submit.prevent="if(confirm('Delete this announcement?')) $el.submit()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition">Delete</button>
                    </form>
                </div>
            @endcan
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-6 max-w-3xl">
        <div class="flex items-center gap-2 mb-3">
            @if($announcement->is_pinned)
                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-primary/10 text-primary">Pinned</span>
            @endif
            @if($announcement->priority === 'urgent')
                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Urgent</span>
            @elseif($announcement->priority === 'important')
                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Important</span>
            @else
                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Normal</span>
            @endif
        </div>

        <h1 class="text-2xl font-bold text-[#333333] mb-2">{{ $announcement->title }}</h1>

        <div class="flex items-center gap-4 text-sm text-[#666666] mb-6">
            <span>By {{ $announcement->creator->name }}</span>
            <span>Published {{ $announcement->published_at?->format('F d, Y g:i A') }}</span>
            @if($announcement->expires_at)
                <span>Expires {{ $announcement->expires_at->format('F d, Y') }}</span>
            @endif
        </div>

        <div class="prose prose-sm max-w-none text-[#333333]">
            {!! nl2br(e($announcement->content)) !!}
        </div>
    </div>
</x-app-layout>
