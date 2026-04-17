<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/school-logo.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#1a4731] via-[#1a4731] to-[#2d6b4a] relative overflow-hidden">
            {{-- Decorative background shapes --}}
            <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
                <div class="absolute -top-20 -right-20 w-96 h-96 bg-white/5 rounded-full"></div>
                <div class="absolute -bottom-32 -left-32 w-[500px] h-[500px] bg-white/5 rounded-full"></div>
                <div class="absolute top-1/4 right-1/4 w-64 h-64 bg-white/[0.03] rounded-full"></div>
            </div>

            <div class="relative z-10 w-full max-w-md mx-4">
                {{-- Logo & school info --}}
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-white/10 backdrop-blur-sm rounded-2xl shadow-lg mb-5 border border-white/20">
                        <img src="{{ asset('images/school-logo.svg') }}" alt="{{ config('school.name') }} Logo" class="w-16 h-16">
                    </div>
                    <h1 class="text-3xl font-bold text-white tracking-tight">{{ config('school.name') }}</h1>
                    <p class="text-sm text-[#f5e6cc] mt-2 font-medium tracking-wide uppercase">Student Learning Hub</p>
                    @if(config('school.address'))
                        <p class="text-xs text-white/50 mt-1">{{ config('school.address') }}</p>
                    @endif
                </div>

                {{-- Card --}}
                <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <div class="px-8 py-8">
                        {{ $slot }}
                    </div>
                    <div class="bg-[#f5e6cc]/30 px-8 py-4 text-center">
                        <p class="text-xs text-[#666666]">&copy; {{ date('Y') }} {{ config('school.name') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
