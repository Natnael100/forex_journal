<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'pipJournal') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-100 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="hidden lg:flex lg:flex-col w-64 bg-slate-900/50 backdrop-blur-xl border-r border-slate-800/50">
            <!-- Logo -->
            <div class="p-6 border-b border-slate-800/50">
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    <span>📊</span>
                    <span>pipJournal</span>
                </h1>
            </div>

            <!-- Navigation -->
            @include('components.navigation')

            <!-- User Profile -->
            <div class="mt-auto p-4 border-t border-slate-800/50">
                <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-slate-800/30">
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->roles->first()?->name ?? 'User' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 text-sm text-left text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Mobile Header -->
            <header class="lg:hidden sticky top-0 z-10 bg-slate-900/95 backdrop-blur-xl border-b border-slate-800/50">
                <div class="flex items-center justify-between px-4 py-3">
                    <h1 class="text-xl font-bold text-white flex items-center gap-2">
                        <span>📊</span>
                        <span>pipJournal</span>
                    </h1>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="container mx-auto px-4 py-8 max-w-7xl">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
