@props([
    'loadingText' => 'Processing...',
])

<button {{ $attributes->merge(['type' => 'submit']) }}
        x-data="{ loading: false }"
        x-on:click="loading = true; $nextTick(() => { if($el.form) $el.form.submit(); })"
        x-bind:disabled="loading"
        x-bind:class="{ 'opacity-75 cursor-not-allowed': loading }">
    <span x-show="!loading">{{ $slot }}</span>
    <span x-show="loading" x-cloak class="flex items-center gap-2">
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        {{ $loadingText }}
    </span>
</button>
