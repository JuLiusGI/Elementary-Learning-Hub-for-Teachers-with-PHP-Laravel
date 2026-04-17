<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-[#333333]">Announcements</h2>
            @can('create', App\Models\Announcement::class)
                <a href="{{ route('announcements.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    New Announcement
                </a>
            @endcan
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="space-y-4">
        @forelse($announcements as $announcement)
            <div class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition {{ !in_array($announcement->id, $readIds) ? 'border-l-4 border-primary' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            @if($announcement->is_pinned)
                                <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M5 5a2 2 0 012-2h6a2 2 0 012 2v2h2a1 1 0 010 2h-1.586l-.707 5.707A2 2 0 0112.72 16H7.28a2 2 0 01-1.987-1.293L4.586 9H3a1 1 0 010-2h2V5z"></path></svg>
                            @endif
                            @if($announcement->priority === 'urgent')
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Urgent</span>
                            @elseif($announcement->priority === 'important')
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Important</span>
                            @endif
                            @if(!in_array($announcement->id, $readIds))
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-primary/10 text-primary">New</span>
                            @endif
                        </div>
                        <a href="{{ route('announcements.show', $announcement) }}" class="text-lg font-semibold text-[#333333] hover:text-primary">
                            {{ $announcement->title }}
                        </a>
                        <p class="text-sm text-[#666666] mt-1 line-clamp-2">{{ Str::limit(strip_tags($announcement->content), 200) }}</p>
                        <div class="flex items-center gap-4 mt-2 text-xs text-[#666666]">
                            <span>By {{ $announcement->creator->name }}</span>
                            <span>{{ $announcement->published_at?->format('M d, Y g:i A') }}</span>
                            @if($announcement->expires_at)
                                <span>Expires: {{ $announcement->expires_at->format('M d, Y') }}</span>
                            @endif
                        </div>
                    </div>
                    @can('update', $announcement)
                        <div class="flex items-center gap-2 ml-4">
                            <a href="{{ route('announcements.edit', $announcement) }}" class="text-xs text-[#666666] hover:text-[#333333]">Edit</a>
                            <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" x-data x-on:submit.prevent="if(confirm('Delete this announcement?')) $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm text-center py-12">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                <p class="text-sm text-[#666666]">No announcements yet.</p>
            </div>
        @endforelse
    </div>

    @if($announcements->hasPages())
        <div class="mt-6">
            {{ $announcements->links() }}
        </div>
    @endif
</x-app-layout>
