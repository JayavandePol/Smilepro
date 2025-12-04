@props(['user'])

<aside class="flex h-full flex-col bg-slate-900 text-slate-100 shadow-2xl">
    <div class="relative px-6 pt-10 pb-6 border-b border-white/10">
        <button @click="sidebarOpen = false" class="absolute right-4 top-4 inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/10 text-white transition hover:bg-white/10 lg:hidden">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 6l12 12M6 18L18 6" />
            </svg>
        </button>
        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Smilepro</p>
        <h1 class="mt-2 text-2xl font-bold">Dashboard</h1>
        <p class="mt-1 text-sm text-slate-400">Welkom terug, {{ $user?->name ?? 'Gebruiker' }}</p>
        <button @click="toggle()" class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/5 px-4 py-1.5 text-xs font-semibold text-white hover:bg-white/10 shadow-lg shadow-white/5">
            <span class="text-base" x-text="dark ? '🌙' : '☀️'"></span>
            <span>Schakel thema</span>
        </button>
    </div>

    @php($links = [
        ['label' => 'Overzicht', 'route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
        ['label' => 'Gebruikers', 'route' => 'dashboard.users', 'match' => 'dashboard/users*', 'icon' => 'M5 7l7-4 7 4M5 7v10l7 4 7-4V7'],
        ['label' => 'Medewerkers', 'route' => 'dashboard.employees', 'match' => 'dashboard/employees*', 'icon' => 'M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2m15-11a4 4 0 1 0-8 0 4 4 0 0 0 8 0'],
        ['label' => 'Beschikbaarheid', 'route' => 'dashboard.availability', 'match' => 'dashboard/availability*', 'icon' => 'M3 8h18M8 3v18m4-9h8m-8 4h5'],
        ['label' => 'Patiënten', 'route' => 'dashboard.patients', 'match' => 'dashboard/patients*', 'icon' => 'M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 0c-4 0-7 2-7 5v2h14v-2c0-3-3-5-7-5Z'],
        ['label' => 'Afspraken', 'route' => 'dashboard.appointments', 'match' => 'dashboard/appointments*', 'icon' => 'M3 5h18M8 3v4m8-4v4m-9 5h6m-6 4h3m-9 6h18V9H3Z'],
        ['label' => 'Facturen', 'route' => 'dashboard.invoices', 'match' => 'dashboard/invoices*', 'icon' => 'M6 4h12v16H6z m0 4h12m-8 4h4'],
        ['label' => 'Berichten', 'route' => 'dashboard.messages', 'match' => 'dashboard/messages*', 'icon' => 'M4 4h16v16H4z m0 4 8 5 8-5']
    ])

    <nav class="flex-1 px-3 py-6 space-y-2 overflow-y-auto">
        @foreach($links as $link)
            @php($active = request()->routeIs($link['route']) || request()->is($link['match']))
            <a
                href="{{ route($link['route']) }}"
                class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition
                    {{ $active ? 'bg-white/15 text-white shadow-lg' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $link['icon'] }}" />
                </svg>
                <span>{{ $link['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="px-6 pt-6 pb-10 border-t border-white/10 space-y-4">
        <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-white/10 flex items-center justify-center text-lg font-semibold">
                {{ strtoupper(\Illuminate\Support\Str::substr($user?->name ?? 'NA', 0, 2)) }}
            </div>
            <div>
                <p class="text-sm font-semibold">{{ $user?->name ?? 'Onbekend' }}</p>
                <p class="text-xs text-slate-400">{{ $user?->email ?? 'Geen e-mail' }}</p>
            </div>
        </div>
        <div class="space-y-2 text-sm">
            <a href="{{ route('profile.edit') }}" class="block rounded-lg border border-white/10 px-4 py-2 text-center font-semibold text-slate-200 hover:bg-white/10">
                Profiel
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-lg bg-rose-500/80 px-4 py-2 font-semibold text-white hover:bg-rose-500">
                    Log uit
                </button>
            </form>
        </div>
    </div>
</aside>
