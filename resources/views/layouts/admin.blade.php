<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Smilepro') }} Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @php($currentUser = $user ?? auth()->user())
        <div
            x-data="{
                dark: localStorage.getItem('smilepro-admin-theme') === 'dark',
                sidebarOpen: window.matchMedia('(min-width: 1024px)').matches,
                toggle() {
                    this.dark = !this.dark;
                    localStorage.setItem('smilepro-admin-theme', this.dark ? 'dark' : 'light');
                }
            }"
            x-init="document.documentElement.classList.toggle('dark', dark); $watch('dark', value => document.documentElement.classList.toggle('dark', value))"
            @keydown.window.escape="sidebarOpen = false"
            class="relative min-h-screen overflow-x-hidden bg-slate-100 transition dark:bg-slate-950 lg:pl-80"
            :class="{'dark': dark}">
            <div
                x-show="sidebarOpen"
                class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm lg:hidden"
                x-transition.opacity
                @click="sidebarOpen = false"
            ></div>

            <div
                class="fixed inset-y-0 left-0 z-40 w-72 shrink-0 overflow-hidden lg:w-80"
                :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen, 'lg:translate-x-0': true}"
                x-transition:enter="transition-transform duration-300 ease-out"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition-transform duration-300 ease-in"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
            >
                <x-admin.sidebar :user="$currentUser" />
            </div>

            <main class="relative min-h-screen w-full overflow-y-auto px-5 py-6 space-y-6 transition lg:px-10">
                <div class="flex items-center justify-between lg:hidden">
                    <button @click="sidebarOpen = true" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:hover:bg-slate-800">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        Menu
                    </button>
                    <button @click="toggle()" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:hover:bg-slate-800">
                        <span class="text-base" x-text="dark ? '🌙' : '☀️'"></span>
                        Thema
                    </button>
                </div>

                @if(session('success'))
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow dark:border-emerald-500/30 dark:bg-emerald-900/30 dark:text-emerald-100">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 shadow dark:border-rose-500/30 dark:bg-rose-900/30 dark:text-rose-100">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </body>
</html>
