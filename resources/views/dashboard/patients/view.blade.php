@extends('layouts.admin')

@section('content')
@php
    $segments = [
        'recent' => 'Bezocht < 30 dagen',
        'inactive' => 'Geen bezoek 30+ dagen',
    ];
@endphp
<div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">Patiënten</p>
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Cliëntenbestand</h1>
            <p class="text-sm text-slate-600 dark:text-slate-300">Lees alle patiëntgegevens inclusief contact en laatste bezoek.</p>
        </div>
        <form method="GET" action="{{ route('dashboard.patients') }}" class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow dark:border-slate-800 dark:bg-slate-900">
            <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">Segment</label>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.patients') }}" class="rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-wide {{ empty($activeSegment) ? 'bg-blue-600 text-white' : 'text-slate-500 hover:text-slate-800 dark:text-slate-300' }}">Alles</a>
                @foreach($segments as $segmentKey => $segmentLabel)
                    <button name="segment" value="{{ $segmentKey }}" type="submit" class="rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-wide {{ ($activeSegment ?? '') === $segmentKey ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700' }}">
                        {{ $segmentLabel }}
                    </button>
                @endforeach
            </div>
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-widest text-slate-400">Totaal</p>
            <p class="mt-2 text-4xl font-semibold text-slate-900 dark:text-white">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-xs text-slate-500">Actieve dossierregels</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 shadow-sm dark:border-emerald-500/30 dark:bg-emerald-900/30">
            <p class="text-xs uppercase tracking-widest text-emerald-700 dark:text-emerald-200">Recent bezocht</p>
            <p class="mt-2 text-4xl font-semibold text-emerald-700 dark:text-emerald-100">{{ $stats['active'] ?? 0 }}</p>
            <p class="text-xs text-emerald-700/80 dark:text-emerald-100/80">Laatste 30 dagen</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 shadow-sm dark:border-amber-500/30 dark:bg-amber-900/30">
            <p class="text-xs uppercase tracking-widest text-amber-700 dark:text-amber-100">Opvolging</p>
            <p class="mt-2 text-4xl font-semibold text-amber-700 dark:text-amber-100">{{ $stats['inactive'] ?? 0 }}</p>
            <p class="text-xs text-amber-700/80 dark:text-amber-100/80">Laatste bezoek 30+ dagen</p>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400">
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                {{ $patients->count() }} zichtbare records
            </div>
            <span>Laatste import: {{ now()->format('d-m-Y H:i') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm dark:divide-slate-800">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-widest text-slate-400 dark:bg-slate-800/60 dark:text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Patiënt</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">Geboortedatum</th>
                        <th class="px-6 py-3">Laatste bezoek</th>
                        <th class="px-6 py-3 text-right">Actie</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white dark:divide-slate-800 dark:bg-slate-900">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $patient->first_name }} {{ $patient->last_name }}</div>
                                <p class="text-xs text-slate-400">#{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                <a href="mailto:{{ $patient->email }}" class="block text-blue-600 hover:underline">{{ $patient->email }}</a>
                                <a href="tel:{{ $patient->phone }}" class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400">{{ $patient->phone }}</a>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $patient->date_of_birth?->format('d-m-Y') ?? 'Onbekend' }}</td>
                            <td class="px-6 py-4">
                                @if($patient->last_visit_at)
                                    <p class="text-slate-700 dark:text-slate-200">{{ $patient->last_visit_at->format('d-m-Y') }}</p>
                                    <p class="text-xs text-slate-400">{{ $patient->last_visit_at->diffForHumans() }}</p>
                                @else
                                    <p class="text-slate-400">Geen data</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="mailto:{{ $patient->email }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Contact</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">Geen patiënten gevonden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
