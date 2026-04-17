<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('announcements.show', $announcement) }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-[#333333]">Edit Announcement</h2>
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

    <form method="POST" action="{{ route('announcements.update', $announcement) }}">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-[#333333] mb-1">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $announcement->title) }}" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-[#333333] mb-1">Content</label>
                    <textarea name="content" id="content" rows="6" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">{{ old('content', $announcement->content) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="priority" class="block text-sm font-medium text-[#333333] mb-1">Priority</label>
                        <select name="priority" id="priority" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                            <option value="normal" {{ old('priority', $announcement->priority) == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="important" {{ old('priority', $announcement->priority) == 'important' ? 'selected' : '' }}>Important</option>
                            <option value="urgent" {{ old('priority', $announcement->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned', $announcement->is_pinned) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="text-sm font-medium text-[#333333]">Pin to top</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="published_at" class="block text-sm font-medium text-[#333333] mb-1">Publish Date</label>
                        <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', $announcement->published_at?->format('Y-m-d\TH:i')) }}" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                    </div>

                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-[#333333] mb-1">Expiry Date <span class="text-[#666666] font-normal">(optional)</span></label>
                        <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at', $announcement->expires_at?->format('Y-m-d\TH:i')) }}" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('announcements.show', $announcement) }}" class="text-sm text-[#666666] hover:text-[#333333]">Cancel</a>
                <x-primary-button>Update Announcement</x-primary-button>
            </div>
        </div>
    </form>
</x-app-layout>
