<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('kinder-assessments.index') }}" class="text-[#666666] hover:text-[#333333]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-[#333333]">Kindergarten Assessment — {{ $quarter }}</h2>
                <p class="text-sm text-[#666666]">Rate each student across 5 developmental domains</p>
            </div>
        </div>
    </x-slot>

    @if($isLocked)
        <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600">These assessments are locked and cannot be edited.</p>
        </div>
    @elseif($isSubmitted)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-700">These assessments have been submitted for approval.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('kinder-assessments.store') }}">
        @csrf
        <input type="hidden" name="quarter" value="{{ $quarter }}">

        @foreach($students as $sIndex => $student)
            <div class="bg-white rounded-lg shadow-sm mb-6 overflow-hidden">
                <div class="px-6 py-3 bg-gray-50 border-b">
                    <h3 class="text-sm font-semibold text-[#333333]">{{ $sIndex + 1 }}. {{ $student->full_name }}</h3>
                </div>
                <input type="hidden" name="assessments[{{ $sIndex }}][student_id]" value="{{ $student->id }}">

                <div class="p-6">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-left text-xs font-medium text-[#666666] uppercase pb-3 w-1/3">Domain</th>
                                @foreach($ratings as $key => $label)
                                    <th class="text-center text-xs font-medium text-[#666666] uppercase pb-3">{{ $label }}</th>
                                @endforeach
                                <th class="text-left text-xs font-medium text-[#666666] uppercase pb-3 w-1/4">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($domains as $domainKey => $domainLabel)
                                @php
                                    $dIndex = array_search($domainKey, array_keys($domains));
                                    $existing = $existingAssessments[$student->id][$domainKey] ?? null;
                                @endphp
                                <tr>
                                    <td class="py-3">
                                        <input type="hidden" name="assessments[{{ $sIndex }}][domains][{{ $dIndex }}][domain]" value="{{ $domainKey }}">
                                        <span class="text-sm text-[#333333]">{{ $domainLabel }}</span>
                                    </td>
                                    @foreach($ratings as $ratingKey => $ratingLabel)
                                        @php
                                            $colors = [
                                                'beginning' => 'peer-checked:bg-yellow-100 peer-checked:text-yellow-800 peer-checked:border-yellow-300',
                                                'developing' => 'peer-checked:bg-blue-100 peer-checked:text-blue-800 peer-checked:border-blue-300',
                                                'proficient' => 'peer-checked:bg-green-100 peer-checked:text-green-800 peer-checked:border-green-300',
                                            ];
                                        @endphp
                                        <td class="py-3 text-center">
                                            <label class="cursor-pointer inline-block">
                                                <input type="radio"
                                                    name="assessments[{{ $sIndex }}][domains][{{ $dIndex }}][rating]"
                                                    value="{{ $ratingKey }}"
                                                    {{ $existing && $existing->rating === $ratingKey ? 'checked' : '' }}
                                                    {{ $isLocked || $isSubmitted ? 'disabled' : '' }}
                                                    class="sr-only peer">
                                                <span class="px-3 py-1.5 text-xs font-medium rounded-full border border-gray-200 text-gray-400 {{ $colors[$ratingKey] }} transition">
                                                    {{ substr($ratingLabel, 0, 1) }}
                                                </span>
                                            </label>
                                        </td>
                                    @endforeach
                                    <td class="py-3">
                                        <input type="text"
                                            name="assessments[{{ $sIndex }}][domains][{{ $dIndex }}][remarks]"
                                            value="{{ $existing->remarks ?? '' }}"
                                            placeholder="Optional"
                                            {{ $isLocked || $isSubmitted ? 'disabled' : '' }}
                                            class="w-full border-gray-300 focus:border-primary focus:ring-primary rounded text-sm">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        @if(!$isLocked && !$isSubmitted)
            <div class="flex items-center justify-between">
                <a href="{{ route('kinder-assessments.index') }}" class="text-sm text-[#666666] hover:text-[#333333]">Cancel</a>
                <div class="flex items-center gap-3">
                    <x-primary-button>
                        Save as Draft
                    </x-primary-button>
                </div>
            </div>
        @endif
    </form>

    <!-- Submit for Approval -->
    @if(!$isLocked && !$isSubmitted && !empty($existingAssessments))
        <div class="mt-4 flex justify-end">
            <form method="POST" action="{{ route('kinder-assessments.submit') }}" x-data x-on:submit.prevent="if(confirm('Submit these assessments for approval?')) $el.submit()">
                @csrf
                <input type="hidden" name="quarter" value="{{ $quarter }}">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Submit for Approval
                </button>
            </form>
        </div>
    @endif
</x-app-layout>
