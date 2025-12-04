@extends('layouts.admin')

@section('content')
@php
    $statusOptions = [
        '' => 'Alle statussen',
        'gepland' => 'Gepland',
        'bezig' => 'Bezig',
        'afgerond' => 'Afgerond',
        'geannuleerd' => 'Geannuleerd',
    ];
    $inProgress = $appointments->where('status', 'bezig')->count();
@endphp
<div class="space-y-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.4em] text-slate-500 dark:text-slate-400">Afspraken</p>
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">Agenda & Statistieken</h1>
            <p class="text-sm text-slate-600 dark:text-slate-300">Overzicht uit stored procedures inclusief realtime count kaarten.</p>
        </div>
        <form method="GET" action="{{ route('dashboard.appointments') }}" class="flex flex-col gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow dark:border-slate-800 dark:bg-slate-900">
            <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">Status</label>
            <div class="flex gap-3">
                <select name="status" class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($activeStatus ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-500">Filter</button>
                @if(!empty($activeStatus))
                    <a href="{{ route('dashboard.appointments') }}" class="text-xs font-semibold uppercase tracking-wide text-blue-600 hover:underline">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-widest text-slate-400">Totaal</p>
            <p class="mt-2 text-4xl font-semibold text-slate-900 dark:text-white">{{ $counts['total'] ?? 0 }}</p>
            <p class="text-xs text-slate-500">Alle afspraken</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 shadow-sm dark:border-emerald-500/30 dark:bg-emerald-900/30">
            <p class="text-xs uppercase tracking-widest text-emerald-700 dark:text-emerald-100">Gepland</p>
            <p class="mt-2 text-4xl font-semibold text-emerald-700 dark:text-emerald-100">{{ $counts['scheduled'] ?? 0 }}</p>
            <p class="text-xs text-emerald-700/80 dark:text-emerald-100/80">Uitstaande afspraken</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <p class="text-xs uppercase tracking-widest text-slate-600 dark:text-slate-300">Afgerond</p>
            <p class="mt-2 text-4xl font-semibold text-slate-700 dark:text-slate-100">{{ $counts['completed'] ?? 0 }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Laatste run</p>
        </div>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 shadow-sm dark:border-rose-500/30 dark:bg-rose-900/30">
            <p class="text-xs uppercase tracking-widest text-rose-700 dark:text-rose-100">Geannuleerd</p>
            <p class="mt-2 text-4xl font-semibold text-rose-700 dark:text-rose-100">{{ $counts['cancelled'] ?? 0 }}</p>
            <p class="text-xs text-rose-700/80 dark:text-rose-100/80">Laatste 30 dagen</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-xl lg:col-span-2 dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-violet-400"></span>
                    {{ $appointments->count() }} zichtbare afspraken
                </div>
                <span>Tijdzone: {{ config('app.timezone') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm dark:divide-slate-800">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-widest text-slate-400 dark:bg-slate-800/60 dark:text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Patiënt</th>
                            <th class="px-6 py-3">Behandelaar</th>
                            <th class="px-6 py-3">Wanneer</th>
                            <th class="px-6 py-3">Behandeling</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Actie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white dark:divide-slate-800 dark:bg-slate-900">
                        @forelse($appointments as $appointment)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $appointment->patient_name }}</div>
                                    <p class="text-xs text-slate-400">{{ $appointment->patient_email }}</p>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $appointment->staff_name }}</td>
                                <td class="px-6 py-4">
                                    <p class="text-slate-700 dark:text-slate-200">{{ $appointment->scheduled_at?->format('d-m-Y H:i') }}</p>
                                    <p class="text-xs text-slate-400">{{ $appointment->scheduled_at?->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $appointment->treatment_type }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'gepland' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200',
                                            'bezig' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200',
                                            'afgerond' => 'bg-slate-100 text-slate-700 dark:bg-slate-800/60 dark:text-slate-200',
                                            'geannuleerd' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200',
                                        ];
                                        $statusLabel = $statusOptions[$appointment->status] ?? ucfirst($appointment->status);
                                    @endphp
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusColors[$appointment->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="mailto:{{ $appointment->patient_email }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Herinner</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">Geen afspraken gevonden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-100 px-6 py-4 dark:border-slate-800">
                <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Aanstaande</p>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Volgende 5 afspraken</h2>
                <p class="text-xs text-slate-500">{{ $inProgress }} afspraken momenteel bezig</p>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($appointments->sortBy(fn($appt) => $appt->scheduled_at)->take(5) as $upcoming)
                    <div class="px-6 py-4">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $upcoming->patient_name }}</p>
                        <p class="text-xs text-slate-400">{{ $upcoming->scheduled_at?->format('d-m-Y H:i') }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $upcoming->treatment_type }} · {{ $upcoming->staff_name }}</p>
                    </div>
                @empty
                    <p class="px-6 py-10 text-center text-slate-500 dark:text-slate-400">Geen aankomende afspraken.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
