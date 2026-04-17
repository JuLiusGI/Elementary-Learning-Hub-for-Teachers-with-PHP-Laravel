@props(['href', 'active' => false])

<a href="{{ $href }}" {{ $attributes->merge(['class' => ($active ? 'bg-primary-light text-white' : 'text-white/80 hover:bg-primary-light hover:text-white') . ' flex items-center px-4 py-3 text-sm rounded-lg transition']) }}>
    {{ $slot }}
</a>
