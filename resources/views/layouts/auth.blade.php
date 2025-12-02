<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'pipJournal') }} - @yield('title', 'Forex Trading Journal')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 text-white antialiased">
    <div class="flex min-h-screen">
        <!-- Left Side - Branding & Info -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <!-- Gradient Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700"></div>
            
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl animate-pulse"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-emerald-300 rounded-full mix-blend-overlay filter blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-center px-16 py-12 text-white">
                <div class="mb-12">
                    <h1 class="text-5xl font-bold mb-4">📊 pipJournal</h1>
                    <p class="text-xl text-emerald-100 leading-relaxed">
                        Your professional forex trading journal. Track, analyze, and improve your trading performance.
                    </p>
                </div>

                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <span class="text-2xl">📈</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Detailed Trade Logging</h3>
                            <p class="text-emerald-100/90">Record every trade with comprehensive metadata and chart screenshots</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <span class="text-2xl">📊</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Performance Analytics</h3>
                            <p class="text-emerald-100/90">Visualize your progress with equity curves, win rates, and profit factors</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <span class="text-2xl">👥</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Expert Feedback</h3>
                            <p class="text-emerald-100/90">Receive professional analysis and insights from performance analysts</p>
                        </div>
                    </div>
                </div>

                <div class="mt-16 pt-8 border-t border-white/20">
                    <p class="text-sm text-emerald-100/75">
                        Trusted by professional traders worldwide to maintain consistency and discipline.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Form Content -->
        <div class="flex-1 flex items-center justify-center px-6 py-12 lg:px-8">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden mb-8 text-center">
                    <h1 class="text-4xl font-bold text-white mb-2">📊 pipJournal</h1>
                    <p class="text-emerald-300">Forex Trading Journal</p>
                </div>

                <!-- Glass Card -->
                <div class="bg-white/5 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 p-8">
                    @yield('content')
                </div>

                <!-- Footer Links -->
                <div class="mt-6 text-center text-sm text-slate-400">
                    @yield('footer')
                </div>
            </div>
        </div>
    </div>
</body>
</html>
