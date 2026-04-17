<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-[#333333]">School Years</h2>
            <a href="{{ route('school-years.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary/90 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                New School Year
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($schoolYears->isEmpty())
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-sm text-[#666666]">No school years configured.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#666666] uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Start Date</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">End Date</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-[#666666] uppercase tracking-wider">Students</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-[#666666] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($schoolYears as $sy)
                            <tr class="hover:bg-gray-50 {{ $sy->is_current ? 'bg-green-50/50' : '' }}">
                                <td class="px-6 py-3">
                                    <span class="text-sm font-medium text-[#333333]">{{ $sy->name }}</span>
                                </td>
                                <td class="px-6 py-3 text-center text-sm text-[#666666]">{{ $sy->start_date->format('M d, Y') }}</td>
                                <td class="px-6 py-3 text-center text-sm text-[#666666]">{{ $sy->end_date->format('M d, Y') }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if($sy->is_current)
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Current</span>
                                    @elseif($sy->is_archived)
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Archived</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Active</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center text-sm text-[#666666]">{{ $sy->students_count }}</td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @unless($sy->is_archived)
                                            <a href="{{ route('school-years.edit', $sy) }}" class="text-xs text-[#666666] hover:text-[#333333]">Edit</a>
                                        @endunless

                                        @unless($sy->is_current)
                                            @unless($sy->is_archived)
                                                <form method="POST" action="{{ route('school-years.activate', $sy) }}" x-data x-on:submit.prevent="if(confirm('Set {{ $sy->name }} as the current school year?')) $el.submit()">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-primary hover:underline">Activate</button>
                                                </form>
                                                <form method="POST" action="{{ route('school-years.archive', $sy) }}" x-data x-on:submit.prevent="if(confirm('Archive {{ $sy->name }}? This cannot be undone.')) $el.submit()">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800">Archive</button>
                                                </form>
                                            @endunless
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
