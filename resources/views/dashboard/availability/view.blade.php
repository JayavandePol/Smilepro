@extends('layouts.admin')

@section('content')
@php
    $statusLabels = [
        'open' => ['label' => 'Beschikbaar', 'color' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'],
        'booked' => ['label' => 'Geboekt', 'color' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200'],
        'blocked' => ['label' => 'Geblokkeerd', 'color' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200'],
    ];
@endphp
<div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">Beschikbaarheid</p>
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Teamplanning</h1>
            <p class="text-sm text-slate-600 dark:text-slate-300">Bekijk alle tijdsloten per medewerker en filter op status.</p>
        </div>
        <form method="GET" action="{{ route('dashboard.availability') }}" class="flex flex-col gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow sm:flex-row sm:items-center dark:border-slate-800 dark:bg-slate-900">
            <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">Statusfilter</label>
            <select name="status" class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                <option value="">Alle statussen</option>
                @foreach($statusLabels as $value => $meta)
                    <option value="{{ $value }}" @selected(($activeStatus ?? '') === $value)>{{ $meta['label'] }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-500">Filter</button>
            @if(!empty($activeStatus))
                <a href="{{ route('dashboard.availability') }}" class="text-xs font-semibold uppercase tracking-wide text-blue-600 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($statusLabels as $key => $meta)
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs uppercase tracking-widest text-slate-400">{{ $meta['label'] }}</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $statusCounts[$key] ?? 0 }}</span>
                    <span class="text-xs text-slate-500">slots</span>
                </div>
                <span class="mt-3 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $meta['color'] }}">
                    {{ ucfirst($key) }}
                </span>
            </div>
        @endforeach
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400">
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                {{ $records->count() }} zichtbare slots
            </div>
            <span>Volgende dagen: {{ $records->pluck('available_on')->unique()->take(3)->map(fn($date) => $date?->format('d-m'))->join(' · ') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm dark:divide-slate-800">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-widest text-slate-400 dark:bg-slate-800/60 dark:text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Medewerker</th>
                        <th class="px-6 py-3">Datum</th>
                        <th class="px-6 py-3">Slot</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Notitie</th>
                        <th class="px-6 py-3 text-right">Contact</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white dark:divide-slate-800 dark:bg-slate-900">
                    @forelse($records as $slot)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $slot->staff_name }}</div>
                                <a href="mailto:{{ $slot->staff_email }}" class="text-xs text-blue-600 hover:underline">{{ $slot->staff_email }}</a>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $slot->available_on?->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $slot->slot }}</td>
                            <td class="px-6 py-4">
                                @php($color = $statusLabels[$slot->status]['color'] ?? 'bg-slate-100 text-slate-600')
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $color }}">
                                    {{ $statusLabels[$slot->status]['label'] ?? ucfirst($slot->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $slot->notes ?? 'Geen notities' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="mailto:{{ $slot->staff_email }}" class="text-xs font-semibold uppercase tracking-widest text-blue-600 hover:underline">Mail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">Geen beschikbaarheid gevonden voor deze filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
