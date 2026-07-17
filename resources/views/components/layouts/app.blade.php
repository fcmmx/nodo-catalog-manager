@props(['title' => null, 'breadcrumbs' => []])
<!DOCTYPE html>
<html lang="es" x-data="{ dark: localStorage.getItem('nodo-theme') === 'dark', sidebarOpen: true, mobileOpen: false }" x-init="$watch('dark', v => { localStorage.setItem('nodo-theme', v ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', v); }); document.documentElement.classList.toggle('dark', dark); sidebarOpen = localStorage.getItem('nodo-sidebar') !== 'closed'; $watch('sidebarOpen', v => localStorage.setItem('nodo-sidebar', v ? 'open' : 'closed'));" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'NODO Catalog Manager' }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('build/app.css') }}?v={{ filemtime(public_path('build/app.css')) }}">
    <script defer src="{{ asset('vendor/alpine/alpine.min.js') }}"></script>
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased dark:bg-slate-950">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-40 flex flex-col border-r border-slate-200 bg-white transition-all duration-200 dark:border-slate-800 dark:bg-slate-900"
            :class="sidebarOpen ? 'w-64' : 'w-[72px]'"
        >
            <div class="flex h-16 items-center gap-2 border-b border-slate-100 px-4 dark:border-slate-800">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-violet-600 text-sm font-bold text-white">N</div>
                <span x-show="sidebarOpen" x-cloak class="truncate text-sm font-semibold text-slate-900 dark:text-white">NODO Catalog Manager</span>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                @foreach (\App\Support\Nav::sections() as $section)
                    <div class="mb-4">
                        <p x-show="sidebarOpen" x-cloak class="mb-1 px-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-600">{{ $section['label'] }}</p>
                        @foreach ($section['items'] as $item)
                            @if (!isset($item['permission']) || auth()->user()->can($item['permission']))
                                <a href="{{ $item['route'] ? route($item['route']) : '#' }}"
                                   class="group mb-0.5 flex items-center gap-3 rounded-lg px-2.5 py-2 text-sm font-medium transition
                                   {{ request()->routeIs($item['active'] ?? '') ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                                    <span class="shrink-0">{!! $item['icon'] !!}</span>
                                    <span x-show="sidebarOpen" x-cloak class="truncate">{{ $item['label'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </nav>

            <button @click="sidebarOpen = !sidebarOpen" class="flex h-12 items-center justify-center border-t border-slate-100 text-slate-400 hover:text-slate-700 dark:border-slate-800 dark:hover:text-slate-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform" :class="{ 'rotate-180': !sidebarOpen }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
            </button>
        </aside>

        {{-- Main --}}
        <div class="flex flex-1 flex-col transition-all duration-200" :class="sidebarOpen ? 'ml-64' : 'ml-[72px]'">
            {{-- Topbar --}}
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-4 border-b border-slate-200 bg-white/80 px-6 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80">
                <form action="{{ route('catalog.products.index') }}" method="GET" class="hidden max-w-md flex-1 md:block">
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                        <input type="search" name="q" placeholder="Buscar productos y servicios…" value="{{ request('q') }}" class="nodo-input pl-9">
                    </div>
                </form>

                <div class="ml-auto flex items-center gap-3">
                    <button @click="dark = !dark" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800" title="Cambiar tema">
                        <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /></svg>
                        <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" /></svg>
                    </button>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" class="relative rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800" title="Actividad reciente">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-80 nodo-card p-2 shadow-lg">
                            <p class="px-2 py-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400">Actividad reciente</p>
                            @forelse (\Spatie\Activitylog\Models\Activity::latest()->limit(5)->get() as $log)
                                <div class="rounded-lg px-2 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <p class="text-slate-700 dark:text-slate-200">{{ $log->description }}</p>
                                    <p class="text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            @empty
                                <p class="px-2 py-3 text-sm text-slate-400">Sin actividad todavía.</p>
                            @endforelse
                            @can('ver actividad')
                                <a href="{{ route('admin.activity.index') }}" class="mt-1 block rounded-lg px-2 py-2 text-center text-xs font-medium text-blue-600 hover:bg-slate-50 dark:hover:bg-slate-800">Ver toda la actividad</a>
                            @endcan
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 rounded-lg p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-violet-600 text-xs font-semibold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-56 nodo-card p-2 shadow-lg">
                            <div class="px-3 py-2">
                                <p class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                            <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800">Mi perfil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40">Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-6 py-6">
                @if (count($breadcrumbs))
                    <nav class="mb-4 flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
                        @foreach ($breadcrumbs as $label => $url)
                            @if (!$loop->last)
                                <a href="{{ $url }}" class="hover:text-slate-900 dark:hover:text-white">{{ $label }}</a>
                                <svg class="h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                            @else
                                <span class="font-medium text-slate-900 dark:text-white">{{ $label }}</span>
                            @endif
                        @endforeach
                    </nav>
                @endif

                @if (session('success'))
                    <div class="mb-4 flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 flex items-center gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
