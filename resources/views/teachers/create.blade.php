<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('teachers.index') }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <h2 class="text-xl font-semibold text-[#333333]">Add Teacher</h2>
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

    <form method="POST" action="{{ route('teachers.store') }}">
        @csrf
        <div class="bg-white rounded-lg shadow-sm p-6 max-w-2xl">
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-[#333333] mb-1">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm" placeholder="e.g., Juan Dela Cruz">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-[#333333] mb-1">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm" placeholder="e.g., teacher@school.local">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-[#333333] mb-1">Password</label>
                    <input type="password" name="password" id="password" required minlength="8" class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm" placeholder="Minimum 8 characters">
                </div>

                <div>
                    <label for="grade_level" class="block text-sm font-medium text-[#333333] mb-1">Assigned Grade Level</label>
                    <select name="grade_level" id="grade_level" required class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded-md text-sm">
                        <option value="">Select Grade Level</option>
                        @foreach(config('school.grade_levels') as $key => $label)
                            <option value="{{ $key }}" {{ old('grade_level') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-sm font-medium text-[#333333]">Active account</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('teachers.index') }}" class="text-sm text-[#666666] hover:text-[#333333]">Cancel</a>
                <x-primary-button>Create Teacher Account</x-primary-button>
            </div>
        </div>
    </form>
</x-app-layout>
