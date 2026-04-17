<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('school-years.index') }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-[#333333]">Edit School Year</h2>
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

    <form method="POST" action="{{ route('school-years.update', $schoolYear) }}">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-[#333333] mb-1">School Year Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $schoolYear->name) }}" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-[#333333] mb-1">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $schoolYear->start_date->format('Y-m-d')) }}" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-[#333333] mb-1">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $schoolYear->end_date->format('Y-m-d')) }}" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('school-years.index') }}" class="text-sm text-[#666666] hover:text-[#333333]">Cancel</a>
                <x-primary-button>Update School Year</x-primary-button>
            </div>
        </div>
    </form>
</x-app-layout>
