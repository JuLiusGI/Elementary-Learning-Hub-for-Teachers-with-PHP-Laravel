<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#1a4731">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Learning Hub">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/school-logo.svg') }}">

        <!-- PWA -->
        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" href="/icons/icon-192.svg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @auth
        <script>
            window.__APP_USER__ = {!! json_encode([
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'role' => auth()->user()->role,
                'grade_level' => auth()->user()->grade_level,
            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
        </script>
        @endauth
    </head>
    <body class="font-sans antialiased min-w-[1024px]">
        <div class="min-h-screen flex bg-[#fefefe]">
            <!-- Sidebar -->
            <aside class="w-64 bg-primary text-white fixed inset-y-0 left-0 z-30 flex flex-col shadow-lg">
                <!-- School Branding -->
                <div class="px-6 py-5 border-b border-primary-light flex items-center gap-3">
                    <img src="{{ asset('images/school-logo.svg') }}" alt="{{ config('school.name') }} Logo" class="w-10 h-10 rounded-full flex-shrink-0">
                    <div>
                        <h1 class="text-lg font-bold leading-tight">{{ config('school.name') }}</h1>
                        <p class="text-xs text-white/60 mt-1">Student Learning Hub</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Students
                    </x-sidebar-link>

                    <div class="pt-4 pb-2">
                        <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Academics</p>
                    </div>

                    <x-sidebar-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Attendance
                    </x-sidebar-link>

                    @if(auth()->user()->grade_level === 'kinder')
                        <x-sidebar-link :href="route('kinder-assessments.index')" :active="request()->routeIs('kinder-assessments.*')">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Assessments
                        </x-sidebar-link>
                    @else
                        <x-sidebar-link :href="route('grades.index')" :active="request()->routeIs('grades.*') || request()->routeIs('kinder-assessments.*')">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Grades
                        </x-sidebar-link>
                    @endif

                    <x-sidebar-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Assignments
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('learning-materials.index')" :active="request()->routeIs('learning-materials.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path></svg>
                        Materials
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Reports
                    </x-sidebar-link>

                    <x-sidebar-link :href="route('announcements.index')" :active="request()->routeIs('announcements.*')">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                        Announcements
                    </x-sidebar-link>

                    @if(auth()->user()->isHeadTeacher())
                        <div class="pt-4 pb-2">
                            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Administration</p>
                        </div>

                        <x-sidebar-link :href="route('approvals.index')" :active="request()->routeIs('approvals.*')">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Approvals
                        </x-sidebar-link>

                        <x-sidebar-link :href="route('teachers.index')" :active="request()->routeIs('teachers.*')">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Teachers
                        </x-sidebar-link>

                        <x-sidebar-link :href="route('school-years.index')" :active="request()->routeIs('school-years.*')">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            School Year
                        </x-sidebar-link>

                        <x-sidebar-link :href="route('promotions.index')" :active="request()->routeIs('promotions.*')">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            Promotions
                        </x-sidebar-link>
                    @endif
                </nav>

                <!-- PWA Install Button -->
                <div x-data x-show="$store.pwa && $store.pwa.canInstall" x-cloak class="px-4 py-2">
                    <button @click="$store.pwa.install()"
                            class="w-full flex items-center gap-2 px-3 py-2 bg-white/10 text-white/80 rounded-lg text-xs hover:bg-white/20 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"></path>
                        </svg>
                        Install App
                    </button>
                </div>

                <!-- Network Status Indicator -->
                <div class="px-4 py-2" x-data x-cloak>
                    <div x-show="typeof $store.network !== 'undefined' && !$store.network.online"
                         class="flex items-center gap-2 px-3 py-2 bg-red-900/30 text-red-200 rounded-lg text-xs">
                        <span class="w-2 h-2 bg-red-400 rounded-full animate-pulse"></span>
                        Offline Mode
                    </div>
                    <div x-show="typeof $store.network !== 'undefined' && $store.network.online && $store.network.syncing"
                         class="flex items-center gap-2 px-3 py-2 bg-yellow-900/30 text-yellow-200 rounded-lg text-xs">
                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Syncing...
                    </div>
                    <div x-show="typeof $store.network !== 'undefined' && $store.network.online && !$store.network.syncing && $store.network.pendingCount > 0"
                         class="flex items-center gap-2 px-3 py-2 bg-yellow-900/30 text-yellow-200 rounded-lg text-xs">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full"></span>
                        <span x-text="$store.network.pendingCount"></span> pending
                    </div>
                </div>

                <!-- User Section -->
                <div class="px-4 py-4 border-t border-primary-light">
                    <div class="flex items-center">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-white/60 truncate">
                                {{ Auth::user()->isHeadTeacher() ? 'Head Teacher' : config('school.grade_levels')[Auth::user()->grade_level] ?? 'Teacher' }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="ml-2 p-2 rounded-lg hover:bg-primary-light text-white/70 hover:text-white transition" title="Logout">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 ml-64">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow-sm border-b">
                        <div class="px-8 py-5">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mx-8 mt-4 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm flex items-center justify-between"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ session('success') }}
                        </span>
                        <button @click="show = false" class="text-green-600 hover:text-green-800 ml-4 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mx-8 mt-4 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm flex items-center justify-between"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ session('error') }}
                        </span>
                        <button @click="show = false" class="text-red-600 hover:text-red-800 ml-4 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mx-8 mt-4 px-4 py-3 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-lg text-sm flex items-center justify-between"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                            {{ session('warning') }}
                        </span>
                        <button @click="show = false" class="text-yellow-600 hover:text-yellow-800 ml-4 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif

                <!-- Page Content -->
                <main class="p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        <!-- Keyboard Navigation -->
        <script>
            document.addEventListener('keydown', function(e) {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') return;
                if (!e.altKey) return;

                const routes = {
                    'd': '{{ route("dashboard") }}',
                    's': '{{ route("students.index") }}',
                    'a': '{{ route("attendance.index") }}',
                    'g': '{{ route("grades.index") }}',
                    'r': '{{ route("reports.index") }}',
                };

                if (routes[e.key]) {
                    e.preventDefault();
                    window.location.href = routes[e.key];
                }
            });
        </script>
    </body>
</html>
